<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\Simple\MyScalarQuery\MyScalarQueryResult>
 */
class MyScalarQuery extends \Spawnia\Sailor\Operation
{
    public static function execute(?string $arg = null): MyScalarQuery\MyScalarQueryResult
    {
        return self::executeOperation(...func_get_args());
    }

    public static function document(): string
    {
        return /* @lang GraphQL */ 'query MyScalarQuery($arg: String) {
          __typename
          scalarWithArg(arg: $arg)
        }';
    }

    public static function endpoint(): string
    {
        return 'simple';
    }
}
