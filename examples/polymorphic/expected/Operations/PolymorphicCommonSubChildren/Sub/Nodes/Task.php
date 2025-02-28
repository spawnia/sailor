<?php declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations\PolymorphicCommonSubChildren\Sub\Nodes;

/**
 * @property string $id
 * @property bool $done
 * @property string $__typename
 * @property \Spawnia\Sailor\Polymorphic\Operations\PolymorphicCommonSubChildren\Sub\Nodes\Node\User|\Spawnia\Sailor\Polymorphic\Operations\PolymorphicCommonSubChildren\Sub\Nodes\Node\Post|\Spawnia\Sailor\Polymorphic\Operations\PolymorphicCommonSubChildren\Sub\Nodes\Node\Task|null $node
 */
class Task extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $id
     * @param bool $done
     * @param \Spawnia\Sailor\Polymorphic\Operations\PolymorphicCommonSubChildren\Sub\Nodes\Node\User|\Spawnia\Sailor\Polymorphic\Operations\PolymorphicCommonSubChildren\Sub\Nodes\Node\Post|\Spawnia\Sailor\Polymorphic\Operations\PolymorphicCommonSubChildren\Sub\Nodes\Node\Task|null $node
     */
    public static function make(
        $id,
        $done,
        $node = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($id !== self::UNDEFINED) {
            $instance->id = $id;
        }
        if ($done !== self::UNDEFINED) {
            $instance->done = $done;
        }
        $instance->__typename = 'Task';
        if ($node !== self::UNDEFINED) {
            $instance->node = $node;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'id' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'done' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'node' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\PolymorphicConverter([
            'User' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\PolymorphicCommonSubChildren\\Sub\\Nodes\\Node\\User',
            'Post' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\PolymorphicCommonSubChildren\\Sub\\Nodes\\Node\\Post',
            'Task' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\PolymorphicCommonSubChildren\\Sub\\Nodes\\Node\\Task',
        ])),
        ];
    }

    public static function endpoint(): string
    {
        return 'polymorphic';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../sailor.php');
    }
}
