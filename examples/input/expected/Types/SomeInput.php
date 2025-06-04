<?php declare(strict_types=1);

namespace Spawnia\Sailor\Input\Types;

/**
 * @property int|string $required
 * @property array<array<int|null>> $matrix
 * @property string|null $optional
 * @property array<string>|null $properties
 * @property \Spawnia\Sailor\Input\Types\SomeInput|null $nested
 */
class SomeInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $required
     * @param array<array<int|null>> $matrix
     * @param string|null $optional
     * @param array<string>|null $properties
     * @param \Spawnia\Sailor\Input\Types\SomeInput|null $nested
     */
    public static function make(
        $required,
        $matrix,
        $optional = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $properties = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $nested = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($required !== self::UNDEFINED) {
            $instance->__set('required', $required);
        }
        if ($matrix !== self::UNDEFINED) {
            $instance->__set('matrix', $matrix);
        }
        if ($optional !== self::UNDEFINED) {
            $instance->__set('optional', $optional);
        }
        if ($properties !== self::UNDEFINED) {
            $instance->__set('properties', $properties);
        }
        if ($nested !== self::UNDEFINED) {
            $instance->__set('nested', $nested);
        }

        return $instance;
    }

    protected function converters(): array
    {
        /** @var array<string, \Spawnia\Sailor\Convert\TypeConverter>|null $converters */
        static $converters;

        return $converters ??= [
            'required' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'matrix' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter))))),
            'optional' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'properties' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter))),
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
