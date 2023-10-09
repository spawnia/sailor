<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\CustomTypes\Operations\MyDefaultDateQuery\MyDefaultDateQueryResult>
 */
class MyDefaultDateQuery extends \Spawnia\Sailor\Operation
{
    /**
     * @param mixed $value
     */
    public static function execute($value): MyDefaultDateQuery\MyDefaultDateQueryResult
    {
        return self::executeOperation(
            $value,
        );
    }

    protected static function converters(): array
    {
        static $converters;

        return $converters ??= [
            ['value', new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ScalarConverter)],
        ];
    }

    public static function document(): string
    {
        return /* @lang GraphQL */ 'query MyDefaultDateQuery($value: DefaultDate!) {
          __typename
          withDefaultDate(value: $value)
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
