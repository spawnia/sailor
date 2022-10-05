<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node;

/**
 * @property string $id
 * @property string $__typename
 * @property \Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\Node\User|\Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\Node\Post|\Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\Node\Task|null $node
 * @property string|null $title
 */
class Post extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $id
     * @param \Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\Node\User|\Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\Node\Post|\Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\Node\Task|null $node
     * @param string|null $title
     */
    public static function make(
        $id,
        $node = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $title = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.'
    ): self {
        $instance = new self;

        if ($id !== self::UNDEFINED) {
            $instance->id = $id;
        }
        $instance->__typename = 'Post';
        if ($node !== self::UNDEFINED) {
            $instance->node = $node;
        }
        if ($title !== self::UNDEFINED) {
            $instance->title = $title;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'id' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'node' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\PolymorphicConverter([
            'User' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\NodeWithFragments\\Node\\Node\\User',
            'Post' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\NodeWithFragments\\Node\\Node\\Post',
            'Task' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\NodeWithFragments\\Node\\Node\\Task',
        ])),
            'title' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
        ];
    }

    public static function endpoint(): string
    {
        return 'polymorphic';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../sailor.php');
    }
}
