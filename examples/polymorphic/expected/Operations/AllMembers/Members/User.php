<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations\AllMembers\Members;

/**
 * @property string $__typename
 * @property string|null $name
 */
class User extends \Spawnia\Sailor\Type\TypedObject
{
    /**
     * @param string|null $name
     */
    public static function make(?string $name = null): self
    {
        $instance = new self;

        $instance->__typename = 'Member';
        $instance->name = $name;

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
}
