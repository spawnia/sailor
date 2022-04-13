<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node;

/**
 * @property \Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\Node\User|\Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\Node\Task|\Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\Node\Post $node
 * @property string $id
 * @property string $__typename
 * @property string|null $title
 */
class Post extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\Node\User|\Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\Node\Task|\Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\Node\Post $node
     * @param string $id
     * @param string|null $title
     */
    public static function make(
        $node,
        $id,
        $title = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.'
    ): self {
        $instance = new self;

        if ($node !== self::UNDEFINED) {
            $instance->node = $node;
        }
        if ($id !== self::UNDEFINED) {
            $instance->id = $id;
        }
        $instance->__typename = 'Node';
        if ($title !== self::UNDEFINED) {
            $instance->title = $title;
        }

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
            'id' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'title' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
        ];
    }

    public static function endpoint(): string
    {
        return 'polymorphic';
    }
}
