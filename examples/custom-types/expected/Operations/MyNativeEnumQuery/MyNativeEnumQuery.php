<?php declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations\MyNativeEnumQuery;

/**
 * @property string $__typename
 * @property \Spawnia\Sailor\CustomTypes\Types\NativeEnum|null $withNativeEnum
 */
class MyNativeEnumQuery extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \Spawnia\Sailor\CustomTypes\Types\NativeEnum|null $withNativeEnum
     */
    public static function make(
        $withNativeEnum = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        $instance->__typename = 'Query';
        if ($withNativeEnum !== self::UNDEFINED) {
            $instance->__set('withNativeEnum', $withNativeEnum);
        }

        return $instance;
    }

    protected function converters(): array
    {
        /** @var array<string, \Spawnia\Sailor\Convert\TypeConverter>|null $converters */
        static $converters;

        return $converters ??= [
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'withNativeEnum' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\CustomTypes\TypeConverters\NativeEnumConverter),
        ];
    }

    public static function endpoint(): string
    {
        return 'custom-types';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../sailor.php');
    }
}
