<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations\UserOrPost;

/**
 * @property \Spawnia\Sailor\Polymorphic\Operations\UserOrPost\Node\Post|\Spawnia\Sailor\Polymorphic\Operations\UserOrPost\Node\Task|\Spawnia\Sailor\Polymorphic\Operations\UserOrPost\Node\User $node
 * @property string $__typename
 */
class UserOrPost extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \Spawnia\Sailor\Polymorphic\Operations\UserOrPost\Node\Post|\Spawnia\Sailor\Polymorphic\Operations\UserOrPost\Node\Task|\Spawnia\Sailor\Polymorphic\Operations\UserOrPost\Node\User $node
     */
    public static function make($node): self
    {
        $instance = new self;

        if ($node !== self::UNDEFINED) {
            $instance->node = $node;
        }
        $instance->__typename = 'Query';

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'node' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\PolymorphicConverter([
            'Post' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\UserOrPost\\Node\\Post',
            'Task' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\UserOrPost\\Node\\Task',
            'User' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\UserOrPost\\Node\\User',
        ])),
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
        ];
    }

    public static function endpoint(): string
    {
        return 'polymorphic';
    }

    public static function config(): string
    {
        return __DIR__ . '/../../../sailor.php';
    }
}
