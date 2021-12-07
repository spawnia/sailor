<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations\NodeMembers;

/**
 * @property string $__typename
 * @property array<int, \Spawnia\Sailor\Polymorphic\Operations\NodeMembers\Members\User|\Spawnia\Sailor\Polymorphic\Operations\NodeMembers\Members\Organization> $members
 */
class NodeMembers extends \Spawnia\Sailor\Type\TypedObject
{
    /** @var string */
    public $__typename;

    /** @var array<int, \Spawnia\Sailor\Polymorphic\Operations\NodeMembers\Members\User|\Spawnia\Sailor\Polymorphic\Operations\NodeMembers\Members\Organization> */
    public $members;

    public function converters(): array
    {
        static $converters;

        return $converters ??= [
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'members' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\PolymorphicConverter([
            'User' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\NodeMembers\\Members\\User',
            'Organization' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\NodeMembers\\Members\\Organization',
        ])))),
        ];
    }

    public function __typenameTypeMapper(): \Spawnia\Sailor\Convert\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter);
    }

    public function membersTypeMapper(): \Spawnia\Sailor\Convert\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\PolymorphicConverter([
            'User' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\NodeMembers\\Members\\User',
            'Organization' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\NodeMembers\\Members\\Organization',
        ]))));
    }
}
