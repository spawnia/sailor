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

    /** @var array<string, Selection> keyed by response name */
    public array $subSelections = [];

    /** @param iterable<ObjectType> $objectTypes */
    public function addObjectTypes(iterable $objectTypes): void
    {
        foreach ($objectTypes as $objectType) {
            $this->objectTypes[$objectType->name] ??= $objectType;
            $this->fields[$objectType->name] ??= [];
        }
    }
}
