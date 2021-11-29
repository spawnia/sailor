<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Enum\Inputs;

use Spawnia\Sailor\Enum\TypeConverters;
use Spawnia\Sailor\Type\Input;
use Spawnia\Sailor\TypeConverter\NullConverter;

/**
 * @property string|null $default
 * @property \Spawnia\Sailor\Enum\Enums\CustomEnum|null $custom
 */
class EnumInput extends Input
{
    protected function converters(): array
    {
        return [
            'default' => new NullConverter(TypeConverters::DefaultEnum()),
            'custom' => new NullConverter(TypeConverters::CustomEnum()),
        ];
    }

    public static function endpoint(): string
    {
        return 'simple';
    }
}
