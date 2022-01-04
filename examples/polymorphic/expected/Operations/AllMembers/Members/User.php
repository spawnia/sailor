<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations\AllMembers\Members;

/**
 * @property string $__typename
 * @property string|null $name
 */
class User extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $name
     */
    public static function make($name = 1.7976931348623157E+308): self
    {
        $instance = new self;

        $instance->__typename = 'Member';
        if ($name !== self::UNDEFINED) {
            $instance->name = $name;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'name' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
        ];
    }

    public static function endpoint(): string
    {
        return 'polymorphic';
    }
}
