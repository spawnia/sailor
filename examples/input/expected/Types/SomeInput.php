<?php declare(strict_types=1);

namespace Spawnia\Sailor\Input\Types;

/**
 * @property int|string $required
 * @property array<array<int|null>> $matrix
 * @property string|null $optional
 * @property \Spawnia\Sailor\Input\Types\SomeInput|null $nested
 */
class SomeInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $required
     * @param array<array<int|null>> $matrix
     * @param string|null $optional
     * @param \Spawnia\Sailor\Input\Types\SomeInput|null $nested
     */
    public static function make(
        $required,
        $matrix,
        $optional = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $nested = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.'
    ): self {
        $instance = new self;

        if ($required !== self::UNDEFINED) {
            $instance->required = $required;
        }
        if ($matrix !== self::UNDEFINED) {
            $instance->matrix = $matrix;
        }
        if ($optional !== self::UNDEFINED) {
            $instance->optional = $optional;
        }
        if ($nested !== self::UNDEFINED) {
            $instance->nested = $nested;
        }

        return $instance;
    }

    protected function converters(): array
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

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../sailor.php');
    }
}
