<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node;

/**
 * @property string $id
 * @property string $__typename
 * @property \Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\Node\Post|\Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\Node\Task|\Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\Node\User|null $node
 * @property string|null $title
 */
class Post extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $id
     * @param \Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\Node\Post|\Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\Node\Task|\Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\Node\User|null $node
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
        $instance->__typename = 'Node';
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
            'Post' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\NodeWithFragments\\Node\\Node\\Post',
            'Task' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\NodeWithFragments\\Node\\Node\\Task',
            'User' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\NodeWithFragments\\Node\\Node\\User',
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
        return __DIR__ . '/../../../../sailor.php';
    }
}
