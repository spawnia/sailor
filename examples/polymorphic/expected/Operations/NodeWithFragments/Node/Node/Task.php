<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\Node;

/**
 * @property string $__typename
 * @property \Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\Node\Node\Post|\Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\Node\Node\Task|\Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\Node\Node\User|null $node
 */
class Task extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\Node\Node\Post|\Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\Node\Node\Task|\Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\Node\Node\User|null $node
     */
    public static function make($node = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.'): self
    {
        $instance = new self;

        $instance->__typename = 'Node';
        if ($node !== self::UNDEFINED) {
            $instance->node = $node;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'node' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\PolymorphicConverter([
            'Post' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\NodeWithFragments\\Node\\Node\\Node\\Post',
            'Task' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\NodeWithFragments\\Node\\Node\\Node\\Task',
            'User' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\NodeWithFragments\\Node\\Node\\Node\\User',
        ])),
        ];
    }

    public static function endpoint(): string
    {
        return 'polymorphic';
    }

    public static function config(): string
    {
        return __DIR__ . '/../../../../../sailor.php';
    }
}
