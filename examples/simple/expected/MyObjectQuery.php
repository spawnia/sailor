<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple;

class MyObjectQuery extends \Spawnia\Sailor\Operation
{
    public static function execute(): MyObjectQuery\MyObjectQueryResult
    {
        $response = self::fetchResponse(...func_get_args());

        return \Spawnia\Sailor\Simple\MyObjectQuery\MyObjectQueryResult::fromResponse($response);
    }

    public static function document(): string
    {
        return <<<GRAPHQL
        query MyObjectQuery {
          singleObject {
            value
          }
        }
        GRAPHQL;
    }

    public static function endpoint(): string
    {
        return 'simple';
    }
}
