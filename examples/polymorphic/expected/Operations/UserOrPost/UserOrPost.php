<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations\UserOrPost;

/**
 * @property \Spawnia\Sailor\Polymorphic\Operations\UserOrPost\Node\User|\Spawnia\Sailor\Polymorphic\Operations\UserOrPost\Node\Post|\Spawnia\Sailor\Polymorphic\Operations\UserOrPost\Node\Task $node
 * @property string $__typename
 */
class UserOrPost extends \Spawnia\Sailor\Type\TypedObject
{
    /**
     * @param \Spawnia\Sailor\Polymorphic\Operations\UserOrPost\Node\User|\Spawnia\Sailor\Polymorphic\Operations\UserOrPost\Node\Post|\Spawnia\Sailor\Polymorphic\Operations\UserOrPost\Node\Task $node
     */
    public static function make(object $node): self
    {
        $instance = new self;

        $instance->node = $node;
        $instance->__typename = 'Query';

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'node' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\PolymorphicConverter([
            'User' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\UserOrPost\\Node\\User',
            'Post' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\UserOrPost\\Node\\Post',
            'Task' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\UserOrPost\\Node\\Task',
        ])),
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
        ];
    }
}
