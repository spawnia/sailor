<?php declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Types;

/**
 * @property string|null $default
 * @property \Spawnia\Sailor\CustomTypes\Types\CustomEnum|null $custom
 */
class EnumInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $default
     * @param \Spawnia\Sailor\CustomTypes\Types\CustomEnum|null $custom
     */
    public static function make(
        $default = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $custom = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self
    {
        $instance = new self;

        if ($default !== self::UNDEFINED) {
            $instance->default = $default;
        }
        if ($custom !== self::UNDEFINED) {
            $instance->custom = $custom;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'default' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'custom' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\CustomTypes\TypeConverters\CustomEnumConverter),
        ];
    }

    public static function endpoint(): string
    {
        return 'custom-types';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../sailor.php');
    }
}
