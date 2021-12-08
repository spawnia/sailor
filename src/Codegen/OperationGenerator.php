<?php declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Language\AST\DocumentNode;
use GraphQL\Language\AST\FieldNode;
use GraphQL\Language\AST\NameNode;
use GraphQL\Language\AST\NodeKind;
use GraphQL\Language\AST\OperationDefinitionNode;
use GraphQL\Language\AST\VariableDefinitionNode;
use GraphQL\Language\Printer;
use GraphQL\Language\Visitor;
use GraphQL\Type\Definition\CompositeType;
use GraphQL\Type\Definition\InputType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\NamedType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\OutputType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\UnionType;
use GraphQL\Type\Introspection;
use GraphQL\Type\Schema;
use GraphQL\Utils\TypeComparators;
use GraphQL\Utils\TypeInfo;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Parameter;
use Nette\PhpGenerator\PhpNamespace;
use Spawnia\Sailor\Convert\PolymorphicConverter;
use Spawnia\Sailor\EndpointConfig;
use Spawnia\Sailor\ErrorFreeResult;
use Spawnia\Sailor\Operation;
use Spawnia\Sailor\Result;
use Spawnia\Sailor\Type\TypeConfig;
use Symfony\Component\VarExporter\VarExporter;

/**
 * @phpstan-import-type PolymorphicMapping from PolymorphicConverter
 */
class OperationGenerator implements ClassGenerator
{
    protected Schema $schema;

    protected DocumentNode $document;

    protected EndpointConfig $endpointConfig;

    protected string $endpointName;

    public function __construct(Schema $schema, DocumentNode $document, EndpointConfig $endpointNameConfig, string $endpointName)
    {
        $this->schema = $schema;
        $this->endpointConfig = $endpointNameConfig;
        $this->endpointName = $endpointName;
        $this->document = $document;
    }

    protected OperationStack $operationStack;

    /**
     * @var array<string, TypeConfig>
     */
    protected array $types;

    /**
     * @var array<int, OperationStack>
     */
    protected array $operationStorage = [];

    /**
     * @var array<int, string>
     */
    protected array $namespaceStack = [];

    public function generate(): iterable
    {
        $this->types = $this->endpointConfig->configureTypes($this->schema, $this->endpointName);
        $this->namespaceStack[] = $this->endpointConfig->operationsNamespace();

        $typeInfo = new TypeInfo($this->schema);

        Visitor::visit(
            $this->document,
            Visitor::visitWithTypeInfo(
                $typeInfo,
                [
                    // A named operation, e.g. "mutation FooMutation", maps to a class
                    NodeKind::OPERATION_DEFINITION => [
                        'enter' => function (OperationDefinitionNode $operationDefinition) use ($typeInfo): void {
                            /**
                             * @var NameNode $nameNode we validated every operation node is named
                             *
                             * @see Generator::ensureOperationsAreNamed()
                             */
                            $nameNode = $operationDefinition->name;
                            $operationName = $nameNode->value;

                            // Generate a class to represent the query/mutation itself
                            $operation = new ClassType($operationName, $this->makeNamespace());

                            // The execute method is the public API of the operation
                            $execute = $operation->addMethod('execute');
                            $execute->setStatic();

                            // It returns a typed result which is a new selection set class
                            $resultName = "{$operationName}Result";

                            // Related classes are put into a nested namespace
                            $this->namespaceStack[] = $operationName;
                            $resultClass = $this->withCurrentNamespace($resultName);

                            // The base class contains most of the logic
                            $operationBaseClass = Operation::class;
                            $operation->setExtends($operationBaseClass);
                            $operation->setComment("@extends \\{$operationBaseClass}<\\{$resultClass}>");

                            $execute->setReturnType($resultClass);
                            $execute->setBody(
                                <<<'PHP'
                                    return self::executeOperation(...func_get_args());
                                    PHP
                            );

                            // Store the actual query string in the operation
                            // TODO minify the query string https://github.com/webonyx/graphql-php/issues/1028
                            $document = $operation->addMethod('document');
                            $document->setStatic();
                            $document->setReturnType('string');
                            $operationString = Printer::doPrint($operationDefinition);
                            $document->setBody(
                                <<<PHP
                                    return /* @lang GraphQL */ '{$operationString}';
                                    PHP
                            );

                            // Set the endpoint this operation belongs to
                            $endpoint = $operation->addMethod('endpoint');
                            $endpoint->setStatic();
                            $endpoint->setReturnType('string');
                            $endpoint->setBody(
                                <<<PHP
                                    return '{$this->endpointName}';
                                    PHP
                            );

                            $result = new ClassType($resultName, $this->makeNamespace());
                            $result->setExtends(Result::class);

                            $setData = $result->addMethod('setData');
                            $setData->setVisibility('protected');
                            $dataParam = $setData->addParameter('data');
                            $dataParam->setType('\\stdClass');
                            $setData->setReturnType('void');
                            $setData->setBody(
                                <<<PHP
                                    \$this->data = {$operationName}::fromStdClass(\$data);
                                    PHP
                            );

                            $dataType = $this->withCurrentNamespace($operationName);

                            $fromData = $result->addMethod('fromData');
                            $fromData->setStatic(true);
                            $dataParam = $fromData->addParameter('data');
                            $dataParam->setType($dataType);
                            $fromData->setReturnType('self');
                            $fromData->addComment(
                                <<<'PHPDOC'
                                Useful for instantiation of successful mocked results.

                                @return static
                                PHPDOC
                            );
                            $fromData->setBody(
                                <<<'PHP'
                                $instance = new static;
                                $instance->data = $data;

                                return $instance;
                                PHP
                            );

                            $dataProp = $result->addProperty('data', null);
                            $dataProp->setType($dataType);
                            $dataProp->setNullable(true);

                            $errorFreeResultName = "{$operationName}ErrorFreeResult";

                            $errorFree = $result->addMethod('errorFree');
                            $errorFree->setVisibility('public');
                            $errorFree->setReturnType(
                                $this->withCurrentNamespace($errorFreeResultName)
                            );
                            $errorFree->setBody(
                                <<<PHP
                                    return {$errorFreeResultName}::fromResult(\$this);
                                    PHP
                            );

                            $errorFreeResult = new ClassType($errorFreeResultName, $this->makeNamespace());
                            $errorFreeResult->setExtends(ErrorFreeResult::class);

                            $errorFreeDataProp = $errorFreeResult->addProperty('data');
                            $errorFreeDataProp->setType(
                                $this->withCurrentNamespace($operationName)
                            );
                            $errorFreeDataProp->setNullable(false);

                            $this->operationStack = new OperationStack($operation);
                            $this->operationStack->result = $result;
                            $this->operationStack->errorFreeResult = $errorFreeResult;

                            /** @var ObjectType $operationType always present in validated schemas */
                            $operationType = $typeInfo->getType();
                            $this->operationStack->pushSelection([
                                $operationType->name => $this->makeObjectLikeBuilder($operationName),
                            ]);
                        },
                        'leave' => function (OperationDefinitionNode $_): void {
                            $this->finishSubtree();

                            // Store the current operation as we continue with the next one
                            $this->operationStorage[] = $this->operationStack;
                        },
                    ],
                    NodeKind::VARIABLE_DEFINITION => [
                        'enter' => function (VariableDefinitionNode $variableDefinition) use ($typeInfo): void {
                            $parameter = new Parameter($variableDefinition->variable->name->value);

                            if (null !== $variableDefinition->defaultValue) {
                                // TODO support default values
                            }

                            /** @var Type & InputType $type */
                            $type = $typeInfo->getInputType();

                            if ($type instanceof NonNull) {
                                $type = $type->getWrappedType();
                            } else {
                                $parameter->setNullable();
                                $parameter->setDefaultValue(null);
                            }

                            if ($type instanceof ListOfType) {
                                $parameter->setType('array');
                            } else {
                                $parameter->setType($this->types[$type->name]->typeReference());
                            }

                            $this->operationStack->addParameterToOperation($parameter);
                        },
                    ],
                    NodeKind::FIELD => [
                        'enter' => function (FieldNode $field) use ($typeInfo): void {
                            // We are only interested in the name that will come from the server
                            $fieldName = null !== $field->alias
                                ? $field->alias->value
                                : $field->name->value;

                            $selectionClasses = $this->operationStack->peekSelection();

                            /** @var Type&OutputType $type */
                            $type = $typeInfo->getType();

                            /** @var Type&NamedType $namedType */
                            $namedType = Type::getNamedType($type);

                            if ($namedType instanceof ObjectType) {
                                // We go one level deeper into the selection set
                                // To avoid naming conflicts, we add on another namespace
                                $this->namespaceStack[] = ucfirst($fieldName);

                                $name = $namedType->name;

                                $phpType = $this->withCurrentNamespace($name);
                                $phpDocType = "\\$phpType";

                                $this->operationStack->pushSelection([
                                    $name => $this->makeObjectLikeBuilder($name),
                                ]);
                                $typeConverter = <<<PHP
                                    {$phpType}
                                    PHP;
                            } elseif ($namedType instanceof InterfaceType || $namedType instanceof UnionType) {
                                // We go one level deeper into the selection set
                                // To avoid naming conflicts, we add on another namespace
                                $this->namespaceStack[] = ucfirst($fieldName);

                                /** @var PolymorphicMapping $mapping */
                                $mapping = [];

                                /** @var array<string, ObjectLikeBuilder> $mappingSelection */
                                $mappingSelection = [];

                                foreach ($this->schema->getPossibleTypes($namedType) as $objectType) {
                                    $name = $objectType->name;

                                    $mapping[$name] = "\\{$this->withCurrentNamespace($name)}";
                                    $mappingSelection[$name] = $this->makeObjectLikeBuilder($name);
                                }

                                $phpType = 'object';
                                $phpDocType = implode('|', $mapping);

                                $this->operationStack->pushSelection($mappingSelection);

                                $mappingCode = VarExporter::export($mapping);
                                $typeConverter = <<<PHP
                                    Spawnia\Sailor\Convert\PolymorphicConverter({$mappingCode})
                                    PHP;
                            } else {
                                $typeConfig = $this->types[$namedType->name];
                                $phpType = $typeConfig->typeReference();
                                $phpDocType = $phpType;
                                $typeConverter = <<<PHP
                                    {$typeConfig->typeConverter()}
                                    PHP;
                            }

                            $parentType = $typeInfo->getParentType();
                            if (null === $parentType) {
                                throw new \Exception("Unable to determine parent type of field {$fieldName}");
                            }

                            // Eases instantiation of mocked results
                            $defaultValue = Introspection::TYPE_NAME_FIELD_NAME === $fieldName
                                ? $parentType->name
                                : null;

                            foreach ($selectionClasses as $name => $selection) {
                                $selectionType = $this->schema->getType($name);
                                if (null === $selectionType) {
                                    throw new \Exception("Unable to determine type of selection {$name}");
                                }

                                if (TypeComparators::isTypeSubTypeOf($this->schema, $selectionType, $parentType)) {
                                    $selection->addProperty(
                                        $fieldName,
                                        $type,
                                        $phpDocType,
                                        $phpType,
                                        $typeConverter,
                                        $defaultValue,
                                    );
                                }
                            }
                        },
                        'leave' => function (FieldNode $_) use ($typeInfo): void {
                            /** @var Type&OutputType $type */
                            $type = $typeInfo->getType();

                            /** @var Type&NamedType $namedType */
                            $namedType = Type::getNamedType($type);

                            if ($namedType instanceof CompositeType) {
                                $this->finishSubtree();
                            }
                        },
                    ],
                ]
            )
        );

        foreach ($this->operationStorage as $stack) {
            yield $stack->operation;
            yield $stack->result;
            yield $stack->errorFreeResult;

            foreach ($stack->selectionStorage as $selection) {
                yield $selection;
            }
        }
    }

    protected function finishSubtree(): void
    {
        // We are done with building this subtree of the selection set,
        // so we move the current selections to storage.
        $this->operationStack->popSelection();

        // The namespace moves up a level
        array_pop($this->namespaceStack);
    }

    protected function makeObjectLikeBuilder(string $name): ObjectLikeBuilder
    {
        return new ObjectLikeBuilder(
            $name,
            $this->currentNamespace()
        );
    }

    protected function makeNamespace(): PhpNamespace
    {
        return new PhpNamespace(
            $this->currentNamespace()
        );
    }

    protected function withCurrentNamespace(string $type): string
    {
        return "{$this->currentNamespace()}\\{$type}";
    }

    protected function currentNamespace(): string
    {
        return implode('\\', $this->namespaceStack);
    }
}
