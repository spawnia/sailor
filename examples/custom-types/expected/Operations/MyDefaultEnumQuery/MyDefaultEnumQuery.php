<?php declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations\MyDefaultEnumQuery;

/**
 * @property string $withDefaultEnum
 * @property string $__typename
 */
class MyDefaultEnumQuery extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $withDefaultEnum
     */
    public static function make($withDefaultEnum): self
    {
        $instance = new self;

        if ($withDefaultEnum !== self::UNDEFINED) {
            $instance->__set('withDefaultEnum', $withDefaultEnum);
        }
        $instance->__typename = 'Query';

        return $instance;
    }

    protected function converters(): array
    {
        /** @var array<string, \Spawnia\Sailor\Convert\TypeConverter>|null $converters */
        static $converters;

        return $converters ??= [
            'withDefaultEnum' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
