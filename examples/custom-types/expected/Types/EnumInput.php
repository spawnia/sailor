<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Types;

/**
 * @property string|null $default
 * @property \Spawnia\Sailor\CustomTypes\Types\CustomEnum|null $custom
 */
class EnumInput extends \Spawnia\Sailor\Type\Input
{
    /**
     * @param string|null $default
     * @param \Spawnia\Sailor\CustomTypes\Types\CustomEnum|null $custom
     */
    public static function make(?string $default = null, ?CustomEnum $custom = null): self
    {
        $instance = new self;

        $instance->default = $default;
        $instance->custom = $custom;

        return $instance;
    }

    public function converters(): array
    {
        return [
            'default' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'custom' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\CustomTypes\TypeConverters\CustomEnumConverter),
        ];
    }

    public static function endpoint(): string
    {
        return 'custom-types';
    }
}
