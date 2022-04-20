<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments;

/**
 * @property \Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\User|\Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\Task|\Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\Post $node
 * @property string $__typename
 */
class NodeWithFragments extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\User|\Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\Task|\Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\Post $node
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
            'User' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\NodeWithFragments\\Node\\User',
            'Task' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\NodeWithFragments\\Node\\Task',
            'Post' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\NodeWithFragments\\Node\\Post',
        ])),
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
        ];
    }

    public static function config(): string
    {
        return '/home/bfranke/projects/sailor/tests/Integration/../../examples/polymorphic/sailor.php';
    }

    public static function endpoint(): string
    {
        return 'polymorphic';
    }
}
