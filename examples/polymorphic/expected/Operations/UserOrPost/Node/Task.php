<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations\UserOrPost\Node;

/**
 * @property string $id
 * @property string $__typename
 */
class Task extends \Spawnia\Sailor\Type\TypedObject
{
    /**
     * @param string $id
     */
    public static function make(string $id): self
    {
        $instance = new self;

        $instance->id = $id;
        $instance->__typename = 'Node';

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'id' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
        ];
    }
}
