<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Types;

/**
 * @property string|null $default
 * @property \Spawnia\Sailor\CustomTypes\Types\CustomEnum|null $custom
 */
class EnumInput extends \Spawnia\Sailor\Type\Input
{
    public function converters(): array
    {
        return [
            'default' => new \Spawnia\Sailor\TypeConverter\NullConverter(new \Spawnia\Sailor\TypeConverter\EnumConverter),
            'custom' => new \Spawnia\Sailor\TypeConverter\NullConverter(new \Spawnia\Sailor\CustomTypes\TypeConverters\CustomEnum),
        ];
    }

    public static function endpoint(): string
    {
        return 'custom-types';
    }
}
