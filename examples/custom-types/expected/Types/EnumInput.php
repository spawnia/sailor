<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Types;

/**
 * @property string|null $default
 * @property \Spawnia\Sailor\CustomTypes\Types\CustomEnum|null $custom
 */
class EnumInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $default
     * @param \Spawnia\Sailor\CustomTypes\Types\CustomEnum|null $custom
     */
    public static function make($default = 1.7976931348623157E+308, $custom = 1.7976931348623157E+308): self
    {
        $instance = new self;

        if ($default !== self::UNDEFINED) {
            $instance->default = $default;
        }
        if ($custom !== self::UNDEFINED) {
            $instance->custom = $custom;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'default' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'custom' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\CustomTypes\TypeConverters\CustomEnumConverter),
        ];
    }
}
