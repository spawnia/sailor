<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\CustomTypes\Operations\MyDefaultEnumQuery\MyDefaultEnumQueryResult>
 */
class MyDefaultEnumQuery extends \Spawnia\Sailor\Operation
{
    /**
     * @param string $value
     */
    public static function execute($value): MyDefaultEnumQuery\MyDefaultEnumQueryResult
    {
        return self::executeOperation(
            $value,
        );
    }

    protected static function converters(): array
    {
        static $converters;

        return $converters ??= [
            ['value', new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter)],
        ];
    }

    public static function document(): string
    {
        return /* @lang GraphQL */ 'query MyDefaultEnumQuery($value: DefaultEnum!) {
          __typename
          withDefaultEnum(value: $value)
        }';
    }

    public static function config(): string
    {
        return '/home/bfranke/projects/sailor/examples/custom-types/sailor.php';
    }

    public static function endpoint(): string
    {
        return 'custom-types';
    }
}
