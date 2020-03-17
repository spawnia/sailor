<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple;

class MyObjectNestedQuery extends \Spawnia\Sailor\Operation
{
    public static function execute(): MyObjectNestedQuery\MyObjectNestedQueryResult
    {
        $response = self::fetchResponse(...func_get_args());

        return \Spawnia\Sailor\Simple\MyObjectNestedQuery\MyObjectNestedQueryResult::fromResponse($response);
    }

    public static function document(): string
    {
        return /* @lang GraphQL */ 'query MyObjectNestedQuery {
          singleObject {
            nested {
              value
            }
          }
        }';
    }

    public static function endpoint(): string
    {
        return 'simple';
    }
}
