<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\Simple\Operations\MyObjectNestedQuery\MyObjectNestedQueryResult>
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
          __typename
          singleObject {
            __typename
            nested {
              __typename
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
