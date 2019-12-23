<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple;

class MyScalarQuery extends \Spawnia\Sailor\Operation
{
    public static function execute(?string $arg = null): MyScalarQuery\MyScalarQueryResult
    {
        $response = self::fetchResponse(...func_get_args());

        return \Spawnia\Sailor\Simple\MyScalarQuery\MyScalarQueryResult::fromResponse($response);
    }

    public static function document(): string
    {
        return /* @lang GraphQL */ 'query MyScalarQuery($arg: String) {
          scalarWithArg(arg: $arg)
        }';
    }

    public static function endpoint(): string
    {
        return 'simple';
    }
}
