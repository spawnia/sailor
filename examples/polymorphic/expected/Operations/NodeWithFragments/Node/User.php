<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node;

/**
 * @property \Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\Node\User|\Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\Node\Task|\Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\Node\Post $node
 * @property string $__typename
 */
class User extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\Node\User|\Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\Node\Task|\Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\Node\Post $node
     */
    public static function make($node): self
    {
        $instance = new self;

        if ($node !== self::UNDEFINED) {
            $instance->node = $node;
        }
        $instance->__typename = 'Node';

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'node' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\PolymorphicConverter([
            'User' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\NodeWithFragments\\Node\\Node\\User',
            'Task' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\NodeWithFragments\\Node\\Node\\Task',
            'Post' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\NodeWithFragments\\Node\\Node\\Post',
        ])),
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
        ];
    }

    public static function endpoint(): string
    {
        return 'polymorphic';
    }
}
