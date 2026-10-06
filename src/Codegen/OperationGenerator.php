<?php declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Language\AST\DocumentNode;
use GraphQL\Language\AST\FieldNode;
use GraphQL\Language\AST\FragmentDefinitionNode;
use GraphQL\Language\AST\FragmentSpreadNode;
use GraphQL\Language\AST\InlineFragmentNode;
use GraphQL\Language\AST\ListTypeNode;
use GraphQL\Language\AST\NamedTypeNode;
use GraphQL\Language\AST\NameNode;
use GraphQL\Language\AST\NonNullTypeNode;
use GraphQL\Language\AST\OperationDefinitionNode;
use GraphQL\Language\AST\SelectionSetNode;
use GraphQL\Language\AST\TypeNode;
use GraphQL\Language\Printer;
use GraphQL\Type\Definition\AbstractType;
use GraphQL\Type\Definition\NullableType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Introspection;
use GraphQL\Type\Schema;
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

    /** @var array<string, OperationDefinitionNode> keyed by operation name */
    protected array $wireOperations = [];

    /** @var array<string, FragmentDefinitionNode> keyed by fragment name */
    protected array $wireFragments = [];

    protected EndpointConfig $endpointConfig;

    /** @var array<string, TypeConfig> */
    protected array $types;

    public function __construct(Schema $schema, DocumentNode $document, DocumentNode $wireDocument, EndpointConfig $endpointConfig)
    {
        $this->schema = $schema;
        $this->document = $document;
        $this->endpointConfig = $endpointConfig;

        foreach ($wireDocument->definitions as $definition) {
            if ($definition instanceof OperationDefinitionNode) {
                $this->wireOperations[self::operationName($definition)] = $definition;
            } elseif ($definition instanceof FragmentDefinitionNode) {
                $this->wireFragments[$definition->name->value] = $definition;
            }
        }
    }

    public function generate(): iterable
    {
        $this->types = $this->endpointConfig->configureTypes($this->schema);
        $collector = new FieldCollector($this->schema, $this->document, $this->types);

        foreach ($this->document->definitions as $definition) {
            if ($definition instanceof OperationDefinitionNode) {
                yield from $this->operationClasses($definition, $collector->collect($definition));
            }
        }
    }

    /** @return iterable<ClassType> */
    protected function operationClasses(OperationDefinitionNode $operation, Selection $selection): iterable
    {
        $operationName = Escaper::escapeClassName(self::operationName($operation));
        $namespace = "{$this->endpointConfig->operationsNamespace()}\\{$operationName}";

        $builder = new OperationBuilder($operationName, $this->endpointConfig->operationsNamespace());
        $builder->extendOperation("{$namespace}\\{$operationName}Result");
        // TODO minify the query string https://github.com/webonyx/graphql-php/issues/1028
        $builder->storeDocument($this->wireDocument($this->wireOperations[self::operationName($operation)]));

        foreach ($operation->variableDefinitions as $variableDefinition) {
            $type = $this->inputType($variableDefinition->type);

            $typeConfig = $this->types[Type::getNamedType($type)->name]; // @phpstan-ignore offsetAccess.invalidOffset, method.nonObject (name is string, but typed as mixed in older graphql-php)
            assert($typeConfig instanceof InputTypeConfig);

            $builder->addVariable(
                $variableDefinition->variable->name->value,
                $type,
                $typeConfig->inputTypeReference(),
                $typeConfig->typeConverter(),
                $variableDefinition->defaultValue,
            );
        }

        yield $builder->build();
        yield $this->resultClass($operationName, $namespace);
        yield $this->errorFreeResultClass($operationName, $namespace);
        yield from $this->selectionClasses($selection, $namespace, $operationName);
    }

    protected function wireDocument(OperationDefinitionNode $operation): string
    {
        $fragments = [];
        $this->collectFragments($operation->selectionSet, $fragments);

        return implode("\n\n", array_map(
            [Printer::class, 'doPrint'],
            [$operation, ...array_values($fragments)]
        ));
    }

    /** @param array<string, FragmentDefinitionNode> $fragments */
    protected function collectFragments(SelectionSetNode $selectionSet, array &$fragments): void
    {
        foreach ($selectionSet->selections as $node) {
            if ($node instanceof FragmentSpreadNode) {
                $name = $node->name->value;
                if (! isset($fragments[$name])) {
                    $fragments[$name] = $this->wireFragments[$name];
                    $this->collectFragments($fragments[$name]->selectionSet, $fragments);
                }
            } elseif ($node instanceof FieldNode || $node instanceof InlineFragmentNode) {
                $subSelectionSet = $node->selectionSet;
                if ($subSelectionSet !== null) {
                    $this->collectFragments($subSelectionSet, $fragments);
                }
            }
        }
    }

    protected function resultClass(string $operationName, string $namespace): ClassType
    {
        $result = new ClassType("{$operationName}Result", new PhpNamespace($namespace));
        $result->setExtends(Result::class);

        $setData = $result->addMethod('setData');
        $setData->setVisibility('protected');
        $dataParam = $setData->addParameter('data');
        $dataParam->setType('\\stdClass');
        $setData->setReturnType('void');
        $setData->setBody(<<<PHP
        \$this->data = {$operationName}::fromStdClass(\$data);
        PHP);

        $dataType = "{$namespace}\\{$operationName}";

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
        $errorFree->setReturnType("{$namespace}\\{$errorFreeResultName}");
        $errorFree->setBody(<<<PHP
        return {$errorFreeResultName}::fromResult(\$this);
        PHP);

        return $result;
    }

    protected function errorFreeResultClass(string $operationName, string $namespace): ClassType
    {
        $errorFreeResult = new ClassType("{$operationName}ErrorFreeResult", new PhpNamespace($namespace));
        $errorFreeResult->setExtends(ErrorFreeResult::class);

        $errorFreeDataProp = $errorFreeResult->addProperty('data');
        $errorFreeDataProp->setType("{$namespace}\\{$operationName}");
        $errorFreeDataProp->setNullable(false);

        return $errorFreeResult;
    }

    /** @return iterable<ClassType> */
    protected function selectionClasses(Selection $selection, string $namespace, ?string $rootClassName = null): iterable
    {
        $typename = new CollectedField(Introspection::TYPE_NAME_FIELD_NAME, Introspection::typeNameMetaFieldDef()->getType());

        foreach ($selection->fields as $typeName => $fields) {
            $builder = new ObjectLikeBuilder($rootClassName ?? $typeName, $namespace, false);

            foreach ([$typename, ...array_values($fields)] as $field) {
                $this->addProperty($builder, $namespace, $typeName, $field);
            }

            yield $builder->build();
        }

        foreach ($selection->subSelections as $responseName => $subSelection) {
            yield from $this->selectionClasses($subSelection, self::subNamespace($namespace, $responseName));
        }
    }

    protected function addProperty(ObjectLikeBuilder $builder, string $namespace, string $typeName, CollectedField $field): void
    {
        $namedType = Type::getNamedType($field->type);
        assert($namedType !== null, 'schema is validated'); // @phpstan-ignore function.alreadyNarrowedType, notIdentical.alwaysTrue (keep for safety across graphql-php versions)

        $typeConfig = $this->types[$namedType->name] ?? null; // @phpstan-ignore offsetAccess.invalidOffset (name is string, but typed as mixed in older graphql-php)
        if ($typeConfig !== null) {
            assert($typeConfig instanceof OutputTypeConfig);
            $phpDocType = $typeConfig->outputTypeReference();
            $typeConverter = $typeConfig->typeConverter();
        } elseif ($namedType instanceof ObjectType) {
            $phpType = self::subNamespace($namespace, $field->responseName) . '\\' . Escaper::escapeNamespaceName($namedType->name);
            $phpDocType = "\\{$phpType}";
            $typeConverter = $phpType;
        } elseif ($namedType instanceof AbstractType) {
            /** @var PolymorphicMapping $mapping */
            $mapping = [];
            foreach ($this->schema->getPossibleTypes($namedType) as $objectType) {
                $mapping[$objectType->name] = '\\' . self::subNamespace($namespace, $field->responseName) . '\\' . Escaper::escapeClassName($objectType->name);
            }

            $phpDocType = implode('|', $mapping);
            $mappingCode = VarExporter::export($mapping);
            $typeConverter = "Spawnia\\Sailor\\Convert\\PolymorphicConverter({$mappingCode})";
        } else {
            throw new \Exception("Unexpected namedType {$namedType->name}."); // @phpstan-ignore encapsedStringPart.nonString (property name on interface)
        }

        // Eases instantiation of mocked results
        $defaultValue = $field->responseName === Introspection::TYPE_NAME_FIELD_NAME
            ? $typeName
            : null;

        $builder->addProperty($field->responseName, $field->type, $phpDocType, $typeConverter, $defaultValue);
    }

    protected function inputType(TypeNode $typeNode): Type
    {
        if ($typeNode instanceof NonNullTypeNode) {
            $nullableType = $this->inputType($typeNode->type);
            assert($nullableType instanceof NullableType, 'the grammar forbids nested non-null');

            return Type::nonNull($nullableType);
        }

        if ($typeNode instanceof ListTypeNode) {
            return Type::listOf($this->inputType($typeNode->type));
        }

        assert($typeNode instanceof NamedTypeNode, 'only named types remain');
        $type = $this->schema->getType($typeNode->name->value);
        assert($type !== null, 'validated against the schema');

        return $type;
    }

    protected static function subNamespace(string $namespace, string $responseName): string
    {
        return "{$namespace}\\" . Escaper::escapeNamespaceName(ucfirst($responseName));
    }

    protected static function operationName(OperationDefinitionNode $operation): string
    {
        $nameNode = $operation->name;
        assert($nameNode instanceof NameNode, 'we validated every operation node is named in Generator::ensureOperationsAreNamed()');

        return $nameNode->value;
    }
}
