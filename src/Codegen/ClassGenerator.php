<?php

declare(strict_types=1);

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
use Spawnia\Sailor\EndpointConfig;
use Spawnia\Sailor\ErrorFreeResult;
use Spawnia\Sailor\Operation;
use Spawnia\Sailor\Result;
use Spawnia\Sailor\TypeConverter;
use Spawnia\Sailor\TypeConverter\PolymorphicConverter;
use Spawnia\Sailor\TypedObject;
use Symfony\Component\VarExporter\VarExporter;

/**
 * @phpstan-import-type PolymorphicMapping from PolymorphicConverter
 */
class ClassGenerator
{
    protected Schema $schema;

    protected EndpointConfig $endpointConfig;

    protected string $endpoint;

    protected OperationStack $operationStack;

    /**
     * @var array<string, \Spawnia\Sailor\TypeConfig>
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

    public function __construct(Schema $schema, EndpointConfig $endpointConfig, string $endpoint)
    {
        $this->schema = $schema;
        $this->endpointConfig = $endpointConfig;
        $this->endpoint = $endpoint;

        $this->types = $endpointConfig->types($schema);
        $this->namespaceStack [] = $endpointConfig->namespace();
    }

    /**
     * @return array<int, OperationStack>
     */
    public function generate(DocumentNode $document): array
    {
        $typeInfo = new TypeInfo($this->schema);

        Visitor::visit(
            $document,
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
                            $this->namespaceStack [] = $operationName;
                            $resultClass = $this->withCurrentNamespace($resultName);

                            // The base class contains most of the logic
                            $operationBaseClass = Operation::class;
                            $operation->setExtends($operationBaseClass);
                            $operation->setComment("@extends \\{$operationBaseClass}<\\{$resultClass}>");

                            $execute->setReturnType($resultClass);
                            $execute->setBody(<<<'PHP'
                            return self::executeOperation(...func_get_args());
                            PHP
                            );

                            // Store the actual query string in the operation
                            // TODO minify the query string
                            $document = $operation->addMethod('document');
                            $document->setStatic();
                            $document->setReturnType('string');
                            $operationString = Printer::doPrint($operationDefinition);
                            $document->setBody(<<<PHP
                            return /* @lang GraphQL */ '{$operationString}';
                            PHP
                            );

                            // Set the endpoint this operation belongs to
                            $endpoint = $operation->addMethod('endpoint');
                            $endpoint->setStatic();
                            $endpoint->setReturnType('string');
                            $endpoint->setBody(<<<PHP
                            return '{$this->endpoint}';
                            PHP
                            );

                            $result = new ClassType($resultName, $this->makeNamespace());
                            $result->setExtends(Result::class);

                            $setData = $result->addMethod('setData');
                            $setData->setVisibility('protected');
                            $dataParam = $setData->addParameter('data');
                            $dataParam->setType('\\stdClass');
                            $setData->setReturnType('void');
                            $setData->setBody(<<<PHP
                            \$this->data = {$operationName}::fromStdClass(\$data);
                            PHP
                            );

                            $dataProp = $result->addProperty('data');
                            $dataProp->setType(
                                $this->withCurrentNamespace($operationName)
                            );
                            $dataProp->setNullable(true);

                            $errorFreeResultName = "{$operationName}ErrorFreeResult";

                            $errorFree = $result->addMethod('errorFree');
                            $errorFree->setVisibility('public');
                            $errorFree->setReturnType(
                                $this->withCurrentNamespace($errorFreeResultName)
                            );
                            $errorFree->setBody(<<<PHP
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
                                $operationType->name => $this->makeTypedObject($operationName),
                            ]);
                        },
                        'leave' => function (OperationDefinitionNode $_): void {
                            $this->finishSubtree();

                            // Store the current operation as we continue with the next one
                            $this->operationStorage [] = $this->operationStack;
                        },
                    ],
                    NodeKind::VARIABLE_DEFINITION => [
                        'enter' => function (VariableDefinitionNode $variableDefinition) use ($typeInfo): void {
                            $parameter = new Parameter($variableDefinition->variable->name->value);

                            if ($variableDefinition->defaultValue !== null) {
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
                                $parameter->setType($this->types[$type->name]->typeReference);
                            }

                            $this->operationStack->addParameterToOperation($parameter);
                        },
                    ],
                    NodeKind::FIELD => [
                        'enter' => function (FieldNode $field) use ($typeInfo): void {
                            // We are only interested in the name that will come from the server
                            $fieldName = $field->alias !== null
                                ? $field->alias->value
                                : $field->name->value;

                            // Included in TypedObject by default
                            if ($fieldName === Introspection::TYPE_NAME_FIELD_NAME) {
                                return;
                            }

                            $selectionClasses = $this->operationStack->peekSelection();

                            /** @var Type&OutputType $type */
                            $type = $typeInfo->getType();

                            /** @var Type&NamedType $namedType */
                            $namedType = Type::getNamedType($type);

                            if ($namedType instanceof ObjectType) {
                                // We go one level deeper into the selection set
                                // To avoid naming conflicts, we add on another namespace
                                $this->namespaceStack [] = ucfirst($fieldName);

                                $name = $namedType->name;

                                $typeReference = "\\{$this->withCurrentNamespace($name)}";

                                $this->operationStack->pushSelection([
                                    $name => $this->makeTypedObject($name),
                                ]);
                                $typeConverter = <<<PHP
                                new {$typeReference}
                                PHP;
                            } elseif ($namedType instanceof InterfaceType || $namedType instanceof UnionType) {
                                // We go one level deeper into the selection set
                                // To avoid naming conflicts, we add on another namespace
                                $this->namespaceStack [] = ucfirst($fieldName);

                                /** @var PolymorphicMapping $mapping */
                                $mapping = [];

                                /** @var array<string, ClassType> $mappingSelection */
                                $mappingSelection = [];

                                foreach ($this->schema->getPossibleTypes($namedType) as $objectType) {
                                    $name = $objectType->name;

                                    $mapping[$name] = "\\{$this->withCurrentNamespace($name)}";
                                    $mappingSelection[$name] = $this->makeTypedObject($name);
                                }

                                $typeReference = implode('|', $mapping);

                                $this->operationStack->pushSelection($mappingSelection);

                                $mappingCode = VarExporter::export($mapping);
                                $typeConverter = <<<PHP
                                new \Spawnia\Sailor\TypeConverter\PolymorphicConverter({$mappingCode})
                                PHP;
                            } else {
                                $typeConfig = $this->types[$namedType->name];
                                $typeReference = $typeConfig->typeReference;
                                $typeConverter = <<<PHP
                                new \\{$typeConfig->typeConverter}
                                PHP;
                            }

                            $parentType = $typeInfo->getParentType();
                            if ($parentType === null) {
                                throw new \Exception("Unable to determine parent type of field {$fieldName}");
                            }

                            $wrappedTypeConverter = TypeConverterWrapper::wrap($type, $typeConverter);

                            foreach ($selectionClasses as $name => $selection) {
                                $selectionType = $this->schema->getType($name);
                                if ($selectionType === null) {
                                    throw new \Exception("Unable to determine type of selection {$name}");
                                }

                                if (TypeComparators::isTypeSubTypeOf($this->schema, $selectionType, $parentType)) {
                                    $fieldProperty = $selection->addProperty($fieldName);
                                    $fieldProperty->setComment('@var '.PhpType::phpDoc($type, $typeReference));

                                    $fieldTypeMapper = $selection->addMethod(FieldTypeMapper::methodName($fieldName));
                                    $fieldTypeMapper->setReturnType(TypeConverter::class);
                                    $fieldTypeMapper->setBody(<<<PHP
                                    static \$converter;
                                    return \$converter ??= {$wrappedTypeConverter};
                                    PHP
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

        return $this->operationStorage;
    }

    protected function finishSubtree(): void
    {
        // We are done with building this subtree of the selection set,
        // so we move the top-most element to the storage
        $this->operationStack->popSelection();

        // The namespace moves up a level
        array_pop($this->namespaceStack);
    }

    protected function makeTypedObject(string $name): ClassType
    {
        $typedObject = new ClassType(
            $name,
            $this->makeNamespace()
        );
        $typedObject->addExtend(TypedObject::class);

        return $typedObject;
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
