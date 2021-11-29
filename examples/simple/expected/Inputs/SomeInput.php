<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Inputs;

use Spawnia\Sailor\TypeConverter\EnumConverter;
use Spawnia\Sailor\TypeConverter\IDConverter;
use Spawnia\Sailor\Type\Input;
use Spawnia\Sailor\TypeConverter\IntConverter;
use Spawnia\Sailor\TypeConverter\ListConverter;
use Spawnia\Sailor\TypeConverter\NonNullConverter;
use Spawnia\Sailor\TypeConverter\NullConverter;
use Spawnia\Sailor\TypeConverter\StringConverter;

/**
 * @property string $id
 * @property string|null $name
 * @property string|null $value
 * @property array<int, array<int, int|null>> $matrix
 * @property \Spawnia\Sailor\Simple\Inputs\SomeInput|null $nested
 */
class SomeInput extends Input
{
    protected function converters(): array
    {
        return [
            'id' => new NonNullConverter(new IDConverter()),
            'name' => new NullConverter(new StringConverter()),
            'value' => new NullConverter(new EnumConverter()),
            'matrix' => new NonNullConverter(new ListConverter(new NonNullConverter(new ListConverter(new NullConverter(new IntConverter()))))),
            'nested' => new NullConverter(new SomeInput()),
        ];
    }

    public static function endpoint(): string
    {
        return 'simple';
    }
}
