<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Types;

/**
 * @property string $id
 * @property string|null $name
 * @property string|null $value
 * @property array<int, array<int, int|null>> $matrix
 * @property \Spawnia\Sailor\Simple\Types\SomeInput|null $nested
 */
class SomeInput extends \Spawnia\Sailor\Type\Input
{
    public function converters(): array
    {
        return [
            'id' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter)),
            'name' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'value' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'matrix' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IntConverter))))),
            'nested' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Simple\Types\SomeInput),
        ];
    }

    public static function endpoint(): string
    {
        return 'simple';
    }
}
