<?php declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations\MyCarbonDateQuery;

/**
 * @property \Carbon\Carbon $withCarbonDate
 * @property string $__typename
 */
class MyCarbonDateQuery extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \Carbon\Carbon $withCarbonDate
     */
    public static function make($withCarbonDate): self
    {
        $instance = new self;

        if ($withCarbonDate !== self::UNDEFINED) {
            $instance->withCarbonDate = $withCarbonDate;
        }
        $instance->__typename = 'Query';

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'withCarbonDate' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\CustomTypes\TypeConverters\CarbonDateConverter),
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
