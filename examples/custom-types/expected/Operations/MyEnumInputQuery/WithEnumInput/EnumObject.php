<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations\MyEnumInputQuery\WithEnumInput;

/**
 * @property string $__typename
 * @property \Spawnia\Sailor\CustomTypes\Types\CustomEnum|null $custom
 * @property string|null $default
 */
class EnumObject extends \Spawnia\Sailor\Type\TypedObject
{
    /**
     * @param \Spawnia\Sailor\CustomTypes\Types\CustomEnum|null $custom
     * @param string|null $default
     */
    public static function make(
        ?\Spawnia\Sailor\CustomTypes\Types\CustomEnum $custom = null,
        ?string $default = null
    ): self {
        $instance = new self;

        $instance->__typename = 'EnumObject';
        $instance->custom = $custom;
        $instance->default = $default;

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'custom' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\CustomTypes\TypeConverters\CustomEnumConverter),
            'default' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
        ];
    }
}
