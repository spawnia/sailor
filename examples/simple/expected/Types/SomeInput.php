<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Types;

/**
 * @property string $id
 * @property array<int, array<int, int|null>> $matrix
 * @property string|null $name
 * @property string|null $value
 * @property \Spawnia\Sailor\Simple\Types\SomeInput|null $nested
 */
class SomeInput extends \Spawnia\Sailor\Type\Input
{
    /**
     * @param string $id
     * @param array<int, array<int, int|null>> $matrix
     * @param string|null $name
     * @param string|null $value
     * @param \Spawnia\Sailor\Simple\Types\SomeInput|null $nested
     */
    public static function make(
        string $id,
        array $matrix,
        ?string $name = null,
        ?string $value = null,
        ?SomeInput $nested = null
    ): self {
        $instance = new self;

        $instance->id = $id;
        $instance->matrix = $matrix;
        $instance->name = $name;
        $instance->value = $value;
        $instance->nested = $nested;

        return $instance;
    }

    public function converters(): array
    {
        return [
            'id' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'matrix' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IntConverter))))),
            'name' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'value' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'nested' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Simple\Types\SomeInput),
        ];
    }

    public static function endpoint(): string
    {
        return 'simple';
    }
}
