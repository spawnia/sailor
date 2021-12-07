<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Enum;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\Enum\MyEnumInputQuery\MyEnumInputQueryResult>
 */
class MyEnumInputQuery extends \Spawnia\Sailor\Operation
{
    public static function execute(?Types\EnumInput $input = null): MyEnumInputQuery\MyEnumInputQueryResult
    {
        return self::executeOperation(...func_get_args());
    }

    public static function document(): string
    {
        return /* @lang GraphQL */ 'query MyEnumInputQuery($input: EnumInput) {
          __typename
          withEnumInput(input: $input) {
            __typename
            custom
            default
          }
        }';
    }

    public static function endpoint(): string
    {
        return 'enum';
    }
}
