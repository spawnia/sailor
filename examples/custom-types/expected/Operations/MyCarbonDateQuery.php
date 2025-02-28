<?php declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\CustomTypes\Operations\MyCarbonDateQuery\MyCarbonDateQueryResult>
 */
class MyCarbonDateQuery extends \Spawnia\Sailor\Operation
{
    /**
     * @param \Carbon\Carbon $value
     */
    public static function execute($value): MyCarbonDateQuery\MyCarbonDateQueryResult
    {
        return self::executeOperation(
            $value,
        );
    }

    protected static function converters(): array
    {
        static $converters;

        return $converters ??= [
            ['value', new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\CustomTypes\TypeConverters\CarbonDateConverter)],
        ];
    }

    public static function document(): string
    {
        return /* @lang GraphQL */ 'query MyCarbonDateQuery($value: CarbonDate!) {
          __typename
          withCarbonDate(value: $value)
        }';
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
