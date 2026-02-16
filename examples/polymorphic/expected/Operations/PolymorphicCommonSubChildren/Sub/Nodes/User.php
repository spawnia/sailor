<?php declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations\PolymorphicCommonSubChildren\Sub\Nodes;

/**
 * @property string $id
 * @property string $__typename
 * @property \Spawnia\Sailor\Polymorphic\Operations\PolymorphicCommonSubChildren\Sub\Nodes\Node\User|\Spawnia\Sailor\Polymorphic\Operations\PolymorphicCommonSubChildren\Sub\Nodes\Node\Post|\Spawnia\Sailor\Polymorphic\Operations\PolymorphicCommonSubChildren\Sub\Nodes\Node\Task|null $node
 * @property string|null $name
 */
class User extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $id
     * @param \Spawnia\Sailor\Polymorphic\Operations\PolymorphicCommonSubChildren\Sub\Nodes\Node\User|\Spawnia\Sailor\Polymorphic\Operations\PolymorphicCommonSubChildren\Sub\Nodes\Node\Post|\Spawnia\Sailor\Polymorphic\Operations\PolymorphicCommonSubChildren\Sub\Nodes\Node\Task|null $node
     * @param string|null $name
     */
    public static function make(
        $id,
        $node = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $name = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($id !== self::UNDEFINED) {
            $instance->__set('id', $id);
        }
        $instance->__typename = 'User';
        if ($node !== self::UNDEFINED) {
            $instance->__set('node', $node);
        }
        if ($name !== self::UNDEFINED) {
            $instance->__set('name', $name);
        }

        return $instance;
    }

    protected function converters(): array
    {
        /** @var array<string, \Spawnia\Sailor\Convert\TypeConverter>|null $converters */
        static $converters;

        return $converters ??= [
            'id' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'node' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\PolymorphicConverter([
            'User' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\PolymorphicCommonSubChildren\\Sub\\Nodes\\Node\\User',
            'Post' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\PolymorphicCommonSubChildren\\Sub\\Nodes\\Node\\Post',
            'Task' => '\\Spawnia\\Sailor\\Polymorphic\\Operations\\PolymorphicCommonSubChildren\\Sub\\Nodes\\Node\\Task',
        ])),
            'name' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
