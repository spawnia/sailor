<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Enum;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\Enum\MyCustomEnumQuery\MyCustomEnumQueryResult>
 */
class MyCustomEnumQuery extends \Spawnia\Sailor\Operation
{
    public static function execute(?Types\CustomEnum $value = null): MyCustomEnumQuery\MyCustomEnumQueryResult
    {
        return self::executeOperation(...func_get_args());
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
        return 'enum';
    }
}
