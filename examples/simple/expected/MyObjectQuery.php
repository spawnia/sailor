<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple;

class MyObjectQuery extends \Spawnia\Sailor\Operation
{
    public static function execute(): MyObjectQuery\MyObjectQueryResult
    {
        return self::executeOperation(...func_get_args());
    }

    public static function document(): string
    {
        return /* @lang GraphQL */ 'query MyObjectQuery {
          singleObject {
            value
            __typename
          }
          __typename
        }';
    }

    public static function endpoint(): string
    {
        return 'simple';
    }
}
