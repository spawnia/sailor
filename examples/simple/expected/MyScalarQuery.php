<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple;

class MyScalarQuery extends \Spawnia\Sailor\Operation
{
    public static function execute(?string $arg = null): MyScalarQuery\MyScalarQueryResult
    {
        return self::executeOperation(...func_get_args());
    }

    public static function document(): string
    {
        return /* @lang GraphQL */ 'query MyScalarQuery($arg: String) {
          scalarWithArg(arg: $arg)
          __typename
        }';
    }

    public static function endpoint(): string
    {
        return 'simple';
    }
}
