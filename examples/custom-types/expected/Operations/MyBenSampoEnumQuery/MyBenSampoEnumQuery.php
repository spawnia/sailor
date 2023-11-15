<?php declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations\MyBenSampoEnumQuery;

/**
 * @property string $__typename
 * @property \Spawnia\Sailor\CustomTypes\Types\BenSampoEnum|null $withBenSampoEnum
 */
class MyBenSampoEnumQuery extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \Spawnia\Sailor\CustomTypes\Types\BenSampoEnum|null $withBenSampoEnum
     */
    public static function make($withBenSampoEnum = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.'): self
    {
        $instance = new self;

        $instance->__typename = 'Query';
        if ($withBenSampoEnum !== self::UNDEFINED) {
            $instance->withBenSampoEnum = $withBenSampoEnum;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'withBenSampoEnum' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\CustomTypes\TypeConverters\BenSampoEnumConverter),
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
