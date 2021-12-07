<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\Simple\MyObjectQuery\MyObjectQueryResult>
 */
class MyObjectQuery extends \Spawnia\Sailor\Operation
{
    public static function execute(): MyObjectQuery\MyObjectQueryResult
    {
        return self::executeOperation(...func_get_args());
    }

    public static function document(): string
    {
        return /* @lang GraphQL */ 'query MyObjectQuery {
          __typename
          singleObject {
            __typename
            value
          }
        }';
    }

    public static function endpoint(): string
    {
        return 'simple';
    }
}
