<?php declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node;

/**
 * @property bool $done
 * @property string $__typename
 * @property \Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\Node\User|\Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\Node\Post|\Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\Node\Task|null $node
 */
class Task extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param bool $done
     * @param \Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\Node\User|\Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\Node\Post|\Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\Node\Node\Task|null $node
     */
    public static function make(
        $done,
        $node = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($done !== self::UNDEFINED) {
            $instance->__set('done', $done);
        }
        $instance->__typename = 'Task';
        if ($node !== self::UNDEFINED) {
            $instance->__set('node', $node);
        }

        return $instance;
    }

    protected function converters(): array
    {
        /** @var array<string, \Spawnia\Sailor\Convert\TypeConverter>|null $converters */
        static $converters;

        return $converters ??= [
            'done' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'node' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\PolymorphicConverter([
            'User' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\NodeWithFragments\\Node\\Node\\User',
            'Post' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\NodeWithFragments\\Node\\Node\\Post',
            'Task' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\NodeWithFragments\\Node\\Node\\Task',
        ])),
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
