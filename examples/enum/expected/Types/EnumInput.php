<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Enum\Types;

/**
 * @property string|null $default
 * @property \Spawnia\Sailor\Enum\Types\CustomEnum|null $custom
 */
class EnumInput extends \Spawnia\Sailor\Type\Input
{
    public function converters(): array
    {
        return [
            'default' => new \Spawnia\Sailor\TypeConverter\NullConverter(new \Spawnia\Sailor\TypeConverter\EnumConverter),
            'custom' => new \Spawnia\Sailor\TypeConverter\NullConverter(new \Spawnia\Sailor\Enum\TypeConverters\CustomEnum),
        ];
    }

    public static function endpoint(): string
    {
        return 'enum';
    }
}
