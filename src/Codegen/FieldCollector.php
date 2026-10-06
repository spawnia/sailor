<?php declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Language\AST\BooleanValueNode;
use GraphQL\Language\AST\DirectiveNode;
use GraphQL\Language\AST\DocumentNode;
use GraphQL\Language\AST\FieldNode;
use GraphQL\Language\AST\FragmentDefinitionNode;
use GraphQL\Language\AST\FragmentSpreadNode;
use GraphQL\Language\AST\InlineFragmentNode;
use GraphQL\Language\AST\NamedTypeNode;
use GraphQL\Language\AST\OperationDefinitionNode;
use GraphQL\Language\AST\SelectionSetNode;
use GraphQL\Language\Printer;
use GraphQL\Type\Definition\CompositeType;
use GraphQL\Type\Definition\Directive;
use GraphQL\Type\Definition\FieldDefinition;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\UnionType;
use GraphQL\Type\Introspection;
use GraphQL\Type\Schema;
use GraphQL\Utils\TypeComparators;
use Spawnia\Sailor\Type\TypeConfig;

/** Merges the fields of an operation as in https://spec.graphql.org/October2021/#CollectFields(), per concrete object type. */
class FieldCollector
{
    protected Schema $schema;

    /** @var array<string, FragmentDefinitionNode> */
    protected array $fragments = [];

    /** @var array<string, TypeConfig> types converted as a whole, without collecting their fields */
    protected array $typeConfigs;

    /** @param array<string, TypeConfig> $typeConfigs */
    public function __construct(Schema $schema, DocumentNode $document, array $typeConfigs)
    {
        $this->schema = $schema;
        $this->typeConfigs = $typeConfigs;

        foreach ($document->definitions as $definition) {
            if ($definition instanceof FragmentDefinitionNode) {
                $this->fragments[$definition->name->value] = $definition;
            }
        }
    }

    public function collect(OperationDefinitionNode $operation): Selection
    {
        $rootType = $this->schema->getOperationType($operation->operation);
        assert($rootType instanceof ObjectType, 'validated against the schema');

        $selection = new Selection();
        $selection->addObjectTypes([$rootType], []);
        $this->collectFields($selection, $operation->selectionSet, $rootType, []);

        return $selection;
    }

    /** @param array<string, true> $conditions */
    protected function collectFields(Selection $selection, SelectionSetNode $selectionSet, CompositeType $scope, array $conditions): void
    {
        foreach ($selectionSet->selections as $node) {
            assert($node instanceof FieldNode || $node instanceof InlineFragmentNode || $node instanceof FragmentSpreadNode);
            $nodeConditions = $conditions + self::conditions($node->directives);
            if ($node instanceof FieldNode) {
                $this->collectField($selection, $node, $scope, $nodeConditions);
            } elseif ($node instanceof InlineFragmentNode) {
                $typeCondition = $node->typeCondition;
                $fragmentScope = $typeCondition === null
                    ? $scope
                    : $this->compositeType($typeCondition);
                $this->collectFields($selection, $node->selectionSet, $fragmentScope, $nodeConditions);
            } else {
                $fragment = $this->fragments[$node->name->value];
                $this->collectFields($selection, $fragment->selectionSet, $this->compositeType($fragment->typeCondition), $nodeConditions);
            }
        }
    }

    /** @param array<string, true> $conditions */
    protected function collectField(Selection $selection, FieldNode $node, CompositeType $scope, array $conditions): void
    {
        $fieldName = $node->name->value;
        if ($fieldName === Introspection::TYPE_NAME_FIELD_NAME) {
            return;
        }

        assert($scope instanceof ObjectType || $scope instanceof InterfaceType, 'unions have no fields besides __typename');
        $type = $this->fieldDefinition($scope, $fieldName)->getType();
        $responseName = $node->alias->value ?? $fieldName;

        foreach ($selection->objectTypes as $typeName => $objectType) {
            if (TypeComparators::isTypeSubTypeOf($this->schema, $objectType, $scope)) {
                $field = $selection->fields[$typeName][$responseName] ??= new CollectedField($responseName, $type);
                $field->conditions[] = $conditions;
            }
        }

        $namedType = Type::getNamedType($type);
        if ($namedType instanceof ObjectType) {
            $objectTypes = [$namedType];
        } elseif ($namedType instanceof InterfaceType || $namedType instanceof UnionType) {
            $objectTypes = $this->schema->getPossibleTypes($namedType);
        } else {
            return;
        }

        if (isset($this->typeConfigs[$namedType->name])) {
            return;
        }

        $subSelectionSet = $node->selectionSet;
        assert($subSelectionSet instanceof SelectionSetNode, 'validated against the schema');

        $subSelection = $selection->subSelections[$responseName] ??= new Selection();
        $subSelection->addObjectTypes($objectTypes, $conditions);
        $this->collectFields($subSelection, $subSelectionSet, $namedType, $conditions);
    }

    /** @param ObjectType|InterfaceType $scope */
    protected function fieldDefinition(Type $scope, string $fieldName): FieldDefinition
    {
        if ($fieldName === Introspection::SCHEMA_FIELD_NAME) {
            return Introspection::schemaMetaFieldDef();
        }
        if ($fieldName === Introspection::TYPE_FIELD_NAME) {
            return Introspection::typeMetaFieldDef();
        }

        return $scope->getField($fieldName);
    }

    protected function compositeType(NamedTypeNode $typeCondition): CompositeType
    {
        $type = $this->schema->getType($typeCondition->name->value);
        assert($type instanceof CompositeType, 'validated against the schema');

        return $type;
    }

    /**
     * @param iterable<DirectiveNode> $directives
     *
     * @return array<string, true>
     */
    protected static function conditions(iterable $directives): array
    {
        $conditions = [];
        foreach ($directives as $directive) {
            $name = $directive->name->value;
            if ($name !== Directive::SKIP_NAME && $name !== Directive::INCLUDE_NAME) {
                continue;
            }

            foreach ($directive->arguments as $argument) {
                $alwaysIncluded = $argument->value instanceof BooleanValueNode
                    && $argument->value->value === ($name === Directive::INCLUDE_NAME);
                if (! $alwaysIncluded) {
                    $conditions[Printer::doPrint($directive)] = true;
                }
            }
        }

        return $conditions;
    }
}
