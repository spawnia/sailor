<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations\UserOrPost\Node;

/**
 * @property string $id
 * @property string $__typename
 * @property string|null $name
 */
class User extends \Spawnia\Sailor\Type\TypedObject
{
    /**
     * @param string $id
     * @param string|null $name
     */
    public static function make(string $id, ?string $name = null): self
    {
        $instance = new self;

        $instance->id = $id;
        $instance->__typename = 'Node';
        $instance->name = $name;

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'id' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'name' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
        ];
    }
}
