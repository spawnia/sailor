<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\CustomTypes\Operations\MyNestedCustomObjectQuery\MyNestedCustomObjectQueryResult>
 */
class MyNestedCustomObjectQuery extends \Spawnia\Sailor\Operation
{
    public static function execute(): MyNestedCustomObjectQuery\MyNestedCustomObjectQueryResult
    {
        return self::executeOperation(
        );
    }

    protected static function converters(): array
    {
        static $converters;

        return $converters ??= [
        ];
    }

    public static function document(): string
    {
        return /* @lang GraphQL */ 'query MyNestedCustomObjectQuery {
          __typename
          withNestedCustomObject {
            __typename
            bar {
              __typename
              foo
            }
            baz {
              __typename
              foo
            }
          }
        }';
    }

    public static function endpoint(): string
    {
        return 'custom-types';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../sailor.php');
    }
}
