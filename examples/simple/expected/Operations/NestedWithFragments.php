<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\Simple\Operations\NestedWithFragments\NestedWithFragmentsResult>
 */
class NestedWithFragments extends \Spawnia\Sailor\Operation
{
    public static function execute(): NestedWithFragments\NestedWithFragmentsResult
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
        return /* @lang GraphQL */ 'query NestedWithFragments {
          __typename
          singleObject {
            __typename
            nested {
              __typename
              ... on SomeObject {
                nested {
                  __typename
                  ... on SomeObject {
                    value
                  }
                }
              }
              ... on SomeObject {
                value
              }
            }
            ... on SomeObject {
              nested {
                __typename
                ... on SomeObject {
                  value
                }
              }
            }
            ... on SomeObject {
              value
            }
          }
        }';
    }

    public static function config(): string
    {
        return '/home/bfranke/projects/sailor/examples/simple/sailor.php';
    }

    public static function endpoint(): string
    {
        return 'simple';
    }
}
