<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations\MyBenSampoEnumQuery;

/**
 * @property string $__typename
 * @property \Spawnia\Sailor\CustomTypes\Types\BenSampoEnum|null $withBenSampoEnum
 */
class MyBenSampoEnumQuery extends \Spawnia\Sailor\Type\TypedObject
{
    /**
     * @param \Spawnia\Sailor\CustomTypes\Types\BenSampoEnum|null $withBenSampoEnum
     */
    public static function make(?\Spawnia\Sailor\CustomTypes\Types\BenSampoEnum $withBenSampoEnum = null): self
    {
        $instance = new self;

        $instance->__typename = 'Query';
        $instance->withBenSampoEnum = $withBenSampoEnum;

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'withBenSampoEnum' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\CustomTypes\TypeConverters\BenSampoEnumConverter),
        ];
    }
}
