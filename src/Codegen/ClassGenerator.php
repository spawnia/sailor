<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Language\AST\DocumentNode;
use GraphQL\Language\AST\FieldNode;
use GraphQL\Language\AST\NodeKind;
use GraphQL\Language\AST\OperationDefinitionNode;
use GraphQL\Language\AST\SelectionSetNode;
use GraphQL\Language\AST\VariableDefinitionNode;
use GraphQL\Language\Printer;
use GraphQL\Language\Visitor;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\InputType;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\OutputType;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Utils\TypeInfo;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Parameter;
use Nette\PhpGenerator\PhpNamespace;
use Spawnia\Sailor\EndpointConfig;
use Spawnia\Sailor\Operation;
use Spawnia\Sailor\Result;
use Spawnia\Sailor\TypedObject;

class ClassGenerator
{
    protected Schema $schema;

    protected EndpointConfig $endpointConfig;

    protected string $endpoint;

    protected OperationStack $operationStack;

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
                        'enter' => function (OperationDefinitionNode $operationDefinition): void {
                            $operationName = $operationDefinition->name->value;

                            // Generate a class to represent the query/mutation itself
                            $operation = new ClassType($operationName, $this->makeNamespace());

                            // The base class contains most of the logic
                            $operation->setExtends(Operation::class);

                            // The execute method is the public API of the operation
                            $execute = $operation->addMethod('execute');
                            $execute->setStatic();

                            // It returns a typed result which is a new selection set class
                            $resultName = "{$operationName}Result";

                            // Related classes are put into a nested namespace
                            $this->namespaceStack [] = $operationName;
                            $resultClass = $this->currentNamespace().'\\'.$resultName;

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
                            $document = $operation->addMethod('endpoint');
                            $document->setStatic();
                            $document->setReturnType('string');
                            $document->setBody(<<<PHP
                            return '{$this->endpoint}';
                            PHP
                            );

                            $operationResult = new ClassType($resultName, $this->makeNamespace());
                            $operationResult->setExtends(Result::class);

                            $setData = $operationResult->addMethod('setData');
                            $setData->setVisibility('protected');
                            $dataParam = $setData->addParameter('data');
                            $setData->setReturnType('void');
                            $dataParam->setType('\\stdClass');
                            $setData->setBody(<<<PHP
                            \$this->data = {$operationName}::fromStdClass(\$data);
                            PHP
                            );

                            $dataProp = $operationResult->addProperty('data');
                            $dataProp->setComment("@var $operationName|null");

                            $this->operationStack = new OperationStack($operation);

                            $this->operationStack->result = $operationResult;

                            $this->operationStack->pushSelection(
                                $this->makeTypedObject($operationName)
                            );
                        },
                        'leave' => function (OperationDefinitionNode $_): void {
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
                            } elseif ($type instanceof ScalarType) {
                                $parameter->setType(PhpType::forScalar($type));
                            } elseif ($type instanceof EnumType) {
                                $parameter->setType(PhpType::forEnum($type));
                            } elseif ($type instanceof InputObjectType) {
                                // TODO create value objects to allow typing inputs strictly
                                $parameter->setType('\stdClass');
                            } else {
                                throw new \Exception('Unsupported type: '.get_class($type));
                            }

                            $this->operationStack->addParameterToOperation($parameter);
                        },
                    ],
                    NodeKind::FIELD => [
                        'enter' => function (FieldNode $field) use ($typeInfo): void {
                            // We are only interested in the key that will come from the server
                            $resultKey = $field->alias !== null
                                ? $field->alias->value
                                : $field->name->value;

                            $selection = $this->operationStack->peekSelection();

                            /** @var Type & OutputType $type */
                            $type = $typeInfo->getType();
                            /** @var Type $namedType */
                            $namedType = Type::getNamedType($type);

                            if ($namedType instanceof ObjectType) {
                                $typedObjectName = ucfirst($resultKey);

                                // We go one level deeper into the selection set
                                // To avoid naming conflicts, we add on another namespace
                                $this->namespaceStack [] = $typedObjectName;
                                $typeReference = '\\'.$this->currentNamespace().'\\'.$typedObjectName;

                                $this->operationStack->pushSelection(
                                    $this->makeTypedObject($typedObjectName)
                                );
                                $typeMapper = <<<PHP
                                static function (\\stdClass \$value): \Spawnia\Sailor\TypedObject {
                                    return {$typeReference}::fromStdClass(\$value);
                                }
                                PHP;
                            } elseif ($namedType instanceof ScalarType) {
                                $typeReference = PhpType::forScalar($namedType);
                                $typeMapper = <<<PHP
                                new \Spawnia\Sailor\Mapper\DirectMapper()
                                PHP;
                            } elseif ($namedType instanceof EnumType) {
                                $typeReference = PhpType::forEnum($namedType);
                                // TODO consider mapping from enum instances
                                $typeMapper = <<<PHP
                                new \Spawnia\Sailor\Mapper\DirectMapper()
                                PHP;
                            } else {
                                throw new \Exception('Unsupported type '.get_class($namedType).' found.');
                            }

                            $field = $selection->addProperty($resultKey);
                            $field->setComment('@var '.PhpType::phpDoc($type, $typeReference));

                            $typeField = $selection->addMethod(self::typeDiscriminatorMethodName($resultKey));
                            $typeField->setReturnType('callable');
                            $typeField->setBody(<<<PHP
                            return {$typeMapper};
                            PHP
                            );
                        },
                    ],
                    NodeKind::SELECTION_SET => [
                        'leave' => function (SelectionSetNode $_): void {
                            // We are done with building this subtree of the selection set,
                            // so we move the top-most element to the storage
                            $this->operationStack->popSelection();

                            // The namespace moves up a level
                            array_pop($this->namespaceStack);
                        },
                    ],
                ]
            )
        );

        return $this->operationStorage;
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

    public static function typeDiscriminatorMethodName(string $propertyKey): string
    {
        return 'type'.ucfirst($propertyKey);
    }

    protected function makeNamespace(): PhpNamespace
    {
        return new PhpNamespace(
            $this->currentNamespace()
        );
    }

    protected function currentNamespace(): string
    {
        return implode('\\', $this->namespaceStack);
    }
}
