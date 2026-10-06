<?php declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Type\Definition\ObjectType;

/** The merged fields at one response path, per concrete object type. */
class Selection
{
    /** @var array<string, ObjectType> */
    public array $objectTypes = [];

    /** @var array<string, array<string, CollectedField>> keyed by object type name, then response name */
    public array $fields = [];

    /** @var array<string, array<int, array<string, true>>> keyed by object type name, per occurrence of the parent field, the conditions it is selected under */
    public array $conditions = [];

    /** @var array<string, Selection> keyed by response name */
    public array $subSelections = [];

    /**
     * @param iterable<ObjectType> $objectTypes
     * @param array<string, true> $conditions
     */
    public function addObjectTypes(iterable $objectTypes, array $conditions): void
    {
        foreach ($objectTypes as $objectType) {
            $this->objectTypes[$objectType->name] ??= $objectType;
            $this->fields[$objectType->name] ??= [];
            $this->conditions[$objectType->name][] = $conditions;
        }
    }

    public function isOmittable(string $typeName, CollectedField $field): bool
    {
        foreach ($this->conditions[$typeName] as $conditions) {
            if (! $field->isSelectedUnder($conditions)) {
                return true;
            }
        }

        return false;
    }
}
