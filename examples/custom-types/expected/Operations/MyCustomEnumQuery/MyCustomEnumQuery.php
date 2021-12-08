<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations\MyCustomEnumQuery;

/**
 * @property string $__typename
 * @property \Spawnia\Sailor\CustomTypes\Types\CustomEnum|null $withCustomEnum
 */
class MyCustomEnumQuery extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \Spawnia\Sailor\CustomTypes\Types\CustomEnum|null $withCustomEnum
     */
    public static function make($withCustomEnum = 1.7976931348623157E+308): self
    {
        $instance = new self;

        $instance->__typename = 'Query';
        if ($withCustomEnum !== self::UNDEFINED) {
            $instance->withCustomEnum = $withCustomEnum;
        }

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
