<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Enum;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\Enum\MyDefaultEnumQuery\MyDefaultEnumQueryResult>
 */
class MyDefaultEnumQuery extends \Spawnia\Sailor\Operation
{
    public static function execute(string $value): MyDefaultEnumQuery\MyDefaultEnumQueryResult
    {
        return self::executeOperation(...func_get_args());
    }

    public static function document(): string
    {
        return /* @lang GraphQL */ 'query MyDefaultEnumQuery($value: DefaultEnum!) {
          withDefaultEnum(value: $value)
          __typename
        }';
    }

    public static function endpoint(): string
    {
        return 'enum';
    }
}
