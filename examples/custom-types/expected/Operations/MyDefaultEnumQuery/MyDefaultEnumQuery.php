<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations\MyDefaultEnumQuery;

/**
 * @property string $withDefaultEnum
 * @property string $__typename
 */
class MyDefaultEnumQuery extends \Spawnia\Sailor\Type\TypedObject
{
    /**
     * @param string $withDefaultEnum
     */
    public static function make(string $withDefaultEnum): self
    {
        $instance = new self;

        $instance->withDefaultEnum = $withDefaultEnum;
        $instance->__typename = 'Query';

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'withDefaultEnum' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
        ];
    }
}
