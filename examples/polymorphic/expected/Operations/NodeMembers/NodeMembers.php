<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations\NodeMembers;

class NodeMembers extends \Spawnia\Sailor\TypedObject
{
    /** @var string */
    public $__typename;

    /** @var array<int, \Spawnia\Sailor\Polymorphic\Operations\NodeMembers\Members\User|\Spawnia\Sailor\Polymorphic\Operations\NodeMembers\Members\Organization> */
    public $members;

    public function __typenameTypeMapper(): \Spawnia\Sailor\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\TypeConverter\NullConverter(new \Spawnia\Sailor\TypeConverter\NonNullConverter(new \Spawnia\Sailor\TypeConverter\StringConverter));
    }

    public function membersTypeMapper(): \Spawnia\Sailor\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\TypeConverter\NullConverter(new \Spawnia\Sailor\TypeConverter\NonNullConverter(new \Spawnia\Sailor\TypeConverter\ListConverter(new \Spawnia\Sailor\TypeConverter\NonNullConverter(new \Spawnia\Sailor\TypeConverter\PolymorphicConverter([
            'User' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\NodeMembers\\Members\\User',
            'Organization' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\NodeMembers\\Members\\Organization',
        ])))));
    }
}
