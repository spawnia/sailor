<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations\MyCustomEnumQuery;

/**
 * @property string $__typename
 * @property \Spawnia\Sailor\CustomTypes\Types\CustomEnum|null $withCustomEnum
 */
class MyCustomEnumQuery extends \Spawnia\Sailor\Type\TypedObject
{
    /**
     * @param \Spawnia\Sailor\CustomTypes\Types\CustomEnum|null $withCustomEnum
     */
    public static function make(?\Spawnia\Sailor\CustomTypes\Types\CustomEnum $withCustomEnum = null): self
    {
        $instance = new self;

        $instance->__typename = 'Query';
        $instance->withCustomEnum = $withCustomEnum;

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'withCustomEnum' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\CustomTypes\TypeConverters\CustomEnumConverter),
        ];
    }
}
