<?php declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations\UserOrPost;

/**
 * @property \Spawnia\Sailor\Polymorphic\Operations\UserOrPost\Node\User|\Spawnia\Sailor\Polymorphic\Operations\UserOrPost\Node\Post|\Spawnia\Sailor\Polymorphic\Operations\UserOrPost\Node\Task $node
 * @property string $__typename
 */
class UserOrPost extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \Spawnia\Sailor\Polymorphic\Operations\UserOrPost\Node\User|\Spawnia\Sailor\Polymorphic\Operations\UserOrPost\Node\Post|\Spawnia\Sailor\Polymorphic\Operations\UserOrPost\Node\Task $node
     */
    public static function make($node): self
    {
        $instance = new self;

        if ($node !== self::UNDEFINED) {
            $instance->__set('node', $node);
        }
        $instance->__typename = 'Query';

        return $instance;
    }

    protected function converters(): array
    {
        /** @var array<string, \Spawnia\Sailor\Convert\TypeConverter>|null $converters */
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

    public static function endpoint(): string
    {
        return 'polymorphic';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../sailor.php');
    }
}
