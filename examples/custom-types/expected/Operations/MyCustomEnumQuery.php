<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\CustomTypes\Operations\MyCustomEnumQuery\MyCustomEnumQueryResult>
 */
class MyCustomEnumQuery extends \Spawnia\Sailor\Operation
{
    /**
     * @param \Spawnia\Sailor\CustomTypes\Types\CustomEnum|null $value
     */
    public static function execute($value = 1.7976931348623157E+308): MyCustomEnumQuery\MyCustomEnumQueryResult
    {
        return self::executeOperation(
            $value,
        );
    }

    protected static function converters(): array
    {
        static $converters;

        return $converters ??= [
            ['value', new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\CustomTypes\TypeConverters\CustomEnumConverter)],
        ];
    }

    public static function document(): string
    {
        return /* @lang GraphQL */ 'query MyCustomEnumQuery($value: CustomEnum) {
          __typename
          withCustomEnum(value: $value)
        }';
    }

    public static function endpoint(): string
    {
        return 'custom-types';
    }
}
