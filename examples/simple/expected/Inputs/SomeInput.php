<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Inputs;

/**
 * @property string $id
 * @property string|null $name
 * @property string|null $value
 * @property array<int, array<int, int|null>> $matrix
 * @property \Spawnia\Sailor\Simple\Inputs\SomeInput|null $nested
 */
class SomeInput extends \Spawnia\Sailor\Type\Input
{
    public function converters(): array
    {
        return [
            'id' => new \Spawnia\Sailor\TypeConverter\NullConverter(new \Spawnia\Sailor\TypeConverter\NonNullConverter(new \Spawnia\Sailor\TypeConverter\IDConverter)),
            'name' => new \Spawnia\Sailor\TypeConverter\NullConverter(new \Spawnia\Sailor\TypeConverter\StringConverter),
            'value' => new \Spawnia\Sailor\TypeConverter\NullConverter(new \Spawnia\Sailor\TypeConverter\EnumConverter),
            'matrix' => new \Spawnia\Sailor\TypeConverter\NullConverter(new \Spawnia\Sailor\TypeConverter\ListConverter(new \Spawnia\Sailor\TypeConverter\NonNullConverter(new \Spawnia\Sailor\TypeConverter\ListConverter(new \Spawnia\Sailor\TypeConverter\NonNullConverter(new \Spawnia\Sailor\TypeConverter\IntConverter))))),
            'nested' => new \Spawnia\Sailor\TypeConverter\NullConverter(new self),
        ];
    }

    public static function endpoint(): string
    {
        return 'simple';
    }
}
