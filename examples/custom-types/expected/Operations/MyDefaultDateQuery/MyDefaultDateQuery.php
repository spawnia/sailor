<?php declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations\MyDefaultDateQuery;

/**
 * @property mixed $withDefaultDate
 * @property string $__typename
 */
class MyDefaultDateQuery extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param mixed $withDefaultDate
     */
    public static function make($withDefaultDate): self
    {
        $instance = new self;

        if ($withDefaultDate !== self::UNDEFINED) {
            $instance->withDefaultDate = $withDefaultDate;
        }
        $instance->__typename = 'Query';

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'withDefaultDate' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
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
