<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations\AllMembers;

/**
 * @property array<int, \Spawnia\Sailor\Polymorphic\Operations\AllMembers\Members\User|\Spawnia\Sailor\Polymorphic\Operations\AllMembers\Members\Organization> $members
 * @property string $__typename
 */
class AllMembers extends \Spawnia\Sailor\Type\TypedObject
{
    /**
     * @param array<int, \Spawnia\Sailor\Polymorphic\Operations\AllMembers\Members\User|\Spawnia\Sailor\Polymorphic\Operations\AllMembers\Members\Organization> $members
     */
    public static function make(array $members): self
    {
        $instance = new self;

        $instance->members = $members;
        $instance->__typename = 'Query';

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'members' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\PolymorphicConverter([
            'User' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\AllMembers\\Members\\User',
            'Organization' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\AllMembers\\Members\\Organization',
        ])))),
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
        ];
    }
}