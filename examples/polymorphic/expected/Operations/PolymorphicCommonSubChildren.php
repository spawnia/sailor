<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\Polymorphic\Operations\PolymorphicCommonSubChildren\PolymorphicCommonSubChildrenResult>
 */
class PolymorphicCommonSubChildren extends \Spawnia\Sailor\Operation
{
    public static function execute(): PolymorphicCommonSubChildren\PolymorphicCommonSubChildrenResult
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
        return /* @lang GraphQL */ 'query PolymorphicCommonSubChildren {
          __typename
          sub {
            __typename
            nodes {
              __typename
              id
              node {
                __typename
                id
              }
              ... on User {
                name
              }
              ... on Post {
                title
              }
              ... on Task {
                done
              }
            }
          }
        }';
    }

    public static function endpoint(): string
    {
        return 'polymorphic';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../sailor.php');
    }
}
