<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\CustomTypes\MyDefaultEnumQuery\MyDefaultEnumQueryResult>
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
          __typename
          withDefaultEnum(value: $value)
        }';
    }

    public static function endpoint(): string
    {
        return 'custom-types';
    }
}
