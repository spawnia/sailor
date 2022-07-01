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
use GraphQL\Type\Definition\NamedType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\OutputType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\UnionType;
use GraphQL\Type\Introspection;
use GraphQL\Type\Schema;
use GraphQL\Utils\TypeComparators;
use GraphQL\Utils\TypeInfo;
use Nette\PhpGenerator\ClassType;
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

    public function __construct(Schema $schema, DocumentNode $document, EndpointConfig $endpointNameConfig)
    {
        $this->schema = $schema;
        $this->document = $document;
        $this->endpointConfig = $endpointNameConfig;
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
        $this->types = $this->endpointConfig->configureTypes($this->schema);
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
                            $operation = new OperationBuilder($operationName, $this->currentNamespace());

                            // It returns a typed result which is a new selection set class
                            $resultName = "{$operationName}Result";

                            // Related classes are put into a nested namespace
                            $this->namespaceStack[] = $operationName;
                            $resultClass = $this->withCurrentNamespace($resultName);

                            // The base class contains most of the logic
                            $operation->extendOperation($resultClass);

                            // TODO minify the query string https://github.com/webonyx/graphql-php/issues/1028
                            $operation->storeDocument(Printer::doPrint($operationDefinition));

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
                            $this->operationStack->setSelection(
                                $this->currentNamespace(),
                                [
                                    $operationType->name => $this->makeObjectLikeBuilder($operationName),
                                ]
                            );
                        },
                        'leave' => function (OperationDefinitionNode $_): void {
                            $this->moveUpNamespace();

                            // Store the current operation as we continue with the next one
                            $this->operationStorage[] = $this->operationStack;
                        },
                    ],
                    NodeKind::VARIABLE_DEFINITION => [
                        'enter' => function (VariableDefinitionNode $variableDefinition) use ($typeInfo): void {
                            $name = $variableDefinition->variable->name->value;

                            /** @var Type&InputType $type */
                            $type = $typeInfo->getInputType();

                            /** @var Type&NamedType $namedType */
                            $namedType = Type::getNamedType($type);
                            $typeConfig = $this->types[$namedType->name];

                            $this->operationStack->operation->addVariable(
                                $name,
                                $type,
                                $typeConfig->typeReference(),
                                $typeConfig->typeConverter(),
                                $variableDefinition->defaultValue,
                            );
                        },
                    ],
                    NodeKind::FIELD => [
                        'enter' => function (FieldNode $field) use ($typeInfo): void {
                            // We are only interested in the name that will come from the server
                            $fieldName = null !== $field->alias
                                ? $field->alias->value
                                : $field->name->value;

                            $selectionClasses = $this->operationStack->selection($this->currentNamespace());

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

                                $this->operationStack->setSelection(
                                    $this->currentNamespace(),
                                    [
                                        $name => $this->makeObjectLikeBuilder($name),
                                    ]
                                );
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

                                $this->operationStack->setSelection(
                                    $this->currentNamespace(),
                                    $mappingSelection
                                );

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

                            /** @var Type&NamedType $parentType */
                            $parentType = $typeInfo->getParentType();

                            foreach ($selectionClasses as $name => $selection) {
                                $selectionType = $this->schema->getType($name);
                                if (null === $selectionType) {
                                    throw new \Exception("Unable to determine type of selection {$name}");
                                }

                                if (TypeComparators::isTypeSubTypeOf($this->schema, $selectionType, $parentType)) {
                                    // Eases instantiation of mocked results
                                    $defaultValue = Introspection::TYPE_NAME_FIELD_NAME === $fieldName
                                        ? $selectionType->name
                                        : null;

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
                                $this->moveUpNamespace();
                            }
                        },
                    ],
                ]
            )
        );

        foreach ($this->operationStorage as $stack) {
            yield $stack->operation->build();
            yield $stack->result;
            yield $stack->errorFreeResult;
            yield from $stack->buildSelections();
        }
    }

    protected function moveUpNamespace(): void
    {
        array_pop($this->namespaceStack);
    }

    protected function makeObjectLikeBuilder(string $name): ObjectLikeBuilder
    {
        return new ObjectLikeBuilder(
            $name,
            $this->currentNamespace(),
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
