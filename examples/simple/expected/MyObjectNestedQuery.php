<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\Simple\MyObjectNestedQuery\MyObjectNestedQueryResult>
 */
class MyObjectNestedQuery extends \Spawnia\Sailor\Operation
{
    public static function execute(): MyObjectNestedQuery\MyObjectNestedQueryResult
    {
        return self::executeOperation(...func_get_args());
    }

    public static function document(): string
    {
        return /* @lang GraphQL */ 'query MyObjectNestedQuery {
          singleObject {
            nested {
              value
              __typename
            }
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
