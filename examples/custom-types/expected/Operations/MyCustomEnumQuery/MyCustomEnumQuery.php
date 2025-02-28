<?php declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations\MyCustomEnumQuery;

/**
 * @property string $__typename
 * @property \Spawnia\Sailor\CustomTypes\Types\CustomEnum|null $withCustomEnum
 */
class MyCustomEnumQuery extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \Spawnia\Sailor\CustomTypes\Types\CustomEnum|null $withCustomEnum
     */
    public static function make(
        $withCustomEnum = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self
    {
        $instance = new self;

        $instance->__typename = 'Query';
        if ($withCustomEnum !== self::UNDEFINED) {
            $instance->withCustomEnum = $withCustomEnum;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'withCustomEnum' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\CustomTypes\TypeConverters\CustomEnumConverter),
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
