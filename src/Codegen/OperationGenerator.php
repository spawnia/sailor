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
use GraphQL\Language\VisitorOperation;
use GraphQL\Type\Definition\CompositeType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\ObjectType;
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
use Spawnia\Sailor\Result;
use Spawnia\Sailor\Type\InputTypeConfig;
use Spawnia\Sailor\Type\OutputTypeConfig;
use Spawnia\Sailor\Type\TypeConfig;
use Symfony\Component\VarExporter\VarExporter;

/** @phpstan-import-type PolymorphicMapping from PolymorphicConverter */
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

    /** @var array<string, TypeConfig> */
    protected array $types;

    /** @var array<int, OperationStack> */
    protected array $operationStorage = [];

    /** @var array<int, string> */
    protected array $namespaceStack;

    public function generate(): iterable
    {
        $this->types = $this->endpointConfig->configureTypes($this->schema);
        $this->namespaceStack = [$this->endpointConfig->operationsNamespace()];

        $typeInfo = new TypeInfo($this->schema);
        $visitorWithTypeInfo = Visitor::visitWithTypeInfo($typeInfo, [ // @phpstan-ignore-line specific node types in callables are not typed well yet
            // A named operation, e.g. "mutation FooMutation", maps to a class
            NodeKind::OPERATION_DEFINITION => [
                'enter' => function (OperationDefinitionNode $operationDefinition) use ($typeInfo): void {
                    $nameNode = $operationDefinition->name;
                    assert($nameNode instanceof NameNode, 'we validated every operation node is named in Generator::ensureOperationsAreNamed()');

                    $operationName = Escaper::escapeClassName($nameNode->value);

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
                    $setData->setBody(<<<PHP
                    \$this->data = {$operationName}::fromStdClass(\$data);
                    PHP);

                    $dataType = $this->withCurrentNamespace($operationName);

                    $fromData = $result->addMethod('fromData');
                    $fromData->setStatic(true);
                    $dataParam = $fromData->addParameter('data');
                    $dataParam->setType($dataType);
                    $fromData->setReturnType('self');
                    $fromData->addComment(<<<'PHPDOC'
                    Useful for instantiation of successful mocked results.

                    @return static
                    PHPDOC);
                    $fromData->setBody(<<<'PHP'
                    $instance = new static;
                    $instance->data = $data;

                    return $instance;
                    PHP);

                    $dataProp = $result->addProperty('data', null);
                    $dataProp->setType($dataType);
                    $dataProp->setNullable(true);

                    $errorFreeResultName = "{$operationName}ErrorFreeResult";

                    $errorFree = $result->addMethod('errorFree');
                    $errorFree->setVisibility('public');
                    $errorFree->setReturnType(
                        $this->withCurrentNamespace($errorFreeResultName)
                    );
                    $errorFree->setBody(<<<PHP
                    return {$errorFreeResultName}::fromResult(\$this);
                    PHP);

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

                    $operationType = $typeInfo->getType();
                    assert($operationType instanceof ObjectType, 'always present in validated schemas');
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

                    $type = $typeInfo->getInputType();
                    assert($type !== null, 'schema is validated');

                    $namedType = Type::getNamedType($type);
                    assert($namedType !== null, 'schema is validated');

                    $typeConfig = $this->types[$namedType->name];
                    assert($typeConfig instanceof InputTypeConfig);

                    $this->operationStack->operation->addVariable(
                        $name,
                        $type,
                        $typeConfig->inputTypeReference(),
                        $typeConfig->typeConverter(),
                        $variableDefinition->defaultValue,
                    );
                },
            ],
            NodeKind::FIELD => [
                'enter' => function (FieldNode $field) use ($typeInfo): ?VisitorOperation {
                    // We are only interested in the name that will come from the server
                    $fieldName = $field->alias->value ?? $field->name->value;

                    $selectionClasses = $this->operationStack->selection($this->currentNamespace());

                    $type = $typeInfo->getType();
                    assert($type !== null, 'schema is validated');

                    $namedType = Type::getNamedType($type);
                    assert($namedType !== null, 'schema is validated');

                    if ($namedType instanceof CompositeType) {
                        // We go one level deeper into the selection set
                        // To avoid naming conflicts, we add on another namespace
                        $this->namespaceStack[] = Escaper::escapeNamespaceName(ucfirst($fieldName));
                    }

                    $stopFurtherTraversal = false;
                    $typeConfig = $this->types[$namedType->name] ?? null;
                    if ($typeConfig !== null) {
                        assert($typeConfig instanceof OutputTypeConfig);
                        $phpDocType = $typeConfig->outputTypeReference();
                        $typeConverter = <<<PHP
                        {$typeConfig->typeConverter()}
                        PHP;

                        $stopFurtherTraversal = true;
                    } elseif ($namedType instanceof ObjectType) {
                        $name = $namedType->name;

                        $phpType = $this->withCurrentNamespace(Escaper::escapeNamespaceName($name));
                        $phpDocType = "\\{$phpType}";

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
                        /** @var PolymorphicMapping $mapping */
                        $mapping = [];

                        /** @var array<string, ObjectLikeBuilder> $mappingSelection */
                        $mappingSelection = [];

                        foreach ($this->schema->getPossibleTypes($namedType) as $objectType) {
                            $name = $objectType->name;
                            $escapedName = Escaper::escapeClassName($name);

                            $mapping[$name] = "\\{$this->withCurrentNamespace($escapedName)}";
                            $mappingSelection[$name] = $this->makeObjectLikeBuilder($escapedName);
                        }

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
                        throw new \Exception("Unexpected namedType {$namedType->name}.");
                    }

                    $parentType = $typeInfo->getParentType();
                    assert($parentType !== null);

                    foreach ($selectionClasses as $name => $selection) {
                        $selectionType = $this->schema->getType($name);
                        if ($selectionType === null) {
                            throw new \Exception("Unable to determine type of selection {$name}");
                        }

                        if (TypeComparators::isTypeSubTypeOf($this->schema, $selectionType, $parentType)) {
                            // Eases instantiation of mocked results
                            $defaultValue = $fieldName === Introspection::TYPE_NAME_FIELD_NAME
                                ? $selectionType->name
                                : null;

                            $selection->addProperty($fieldName, $type, $phpDocType, $typeConverter, $defaultValue);
                        }
                    }

                    if ($stopFurtherTraversal) {
                        if ($namedType instanceof CompositeType) {
                            $this->moveUpNamespace();
                        }

                        return Visitor::skipNode();
                    }

                    return null;
                },
                'leave' => function (FieldNode $_) use ($typeInfo): void {
                    $type = $typeInfo->getType();
                    assert($type !== null, 'schema is validated');

                    $namedType = Type::getNamedType($type);
                    assert($namedType !== null, 'schema is validated');

                    if ($namedType instanceof CompositeType) {
                        $this->moveUpNamespace();
                    }
                },
            ],
        ]);
        Visitor::visit($this->document, $visitorWithTypeInfo);

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
            false,
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
