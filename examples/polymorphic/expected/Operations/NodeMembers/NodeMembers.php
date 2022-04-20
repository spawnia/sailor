<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations\NodeMembers;

/**
 * @property array<int, \Spawnia\Sailor\Polymorphic\Operations\NodeMembers\Members\User|\Spawnia\Sailor\Polymorphic\Operations\NodeMembers\Members\Organization> $members
 * @property string $__typename
 */
class NodeMembers extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param array<int, \Spawnia\Sailor\Polymorphic\Operations\NodeMembers\Members\User|\Spawnia\Sailor\Polymorphic\Operations\NodeMembers\Members\Organization> $members
     */
    public static function make($members): self
    {
        $instance = new self;

        if ($members !== self::UNDEFINED) {
            $instance->members = $members;
        }
        $instance->__typename = 'Query';

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'members' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\PolymorphicConverter([
            'User' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\NodeMembers\\Members\\User',
            'Organization' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\NodeMembers\\Members\\Organization',
        ])))),
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
        ];
    }

    public static function config(): string
    {
        return '/home/bfranke/projects/sailor/examples/polymorphic/sailor.php';
    }

    public static function endpoint(): string
    {
        return 'polymorphic';
    }
}
