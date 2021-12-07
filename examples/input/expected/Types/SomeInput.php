<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Input\Types;

/**
 * @property string $required
 * @property array<int, array<int, int|null>> $matrix
 * @property string|null $optional
 * @property \Spawnia\Sailor\Input\Types\SomeInput|null $nested
 */
class SomeInput extends \Spawnia\Sailor\Type\TypedObject
{
    /**
     * @param string $required
     * @param array<int, array<int, int|null>> $matrix
     * @param string|null $optional
     * @param \Spawnia\Sailor\Input\Types\SomeInput|null $nested
     */
    public static function make(
        string $required,
        array $matrix,
        ?string $optional = null,
        ?SomeInput $nested = null
    ): self {
        $instance = new self;

        $instance->required = $required;
        $instance->matrix = $matrix;
        $instance->optional = $optional;
        $instance->nested = $nested;

        return $instance;
    }

    public function converters(): array
    {
        static $converters;

        return $converters ??= [
            'required' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'matrix' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter))))),
            'optional' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'nested' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Input\Types\SomeInput),
        ];
    }

    public static function endpoint(): string
    {
        return 'input';
    }
}
