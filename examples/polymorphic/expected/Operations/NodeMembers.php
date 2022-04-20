<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\Polymorphic\Operations\NodeMembers\NodeMembersResult>
 */
class NodeMembers extends \Spawnia\Sailor\Operation
{
    public static function execute(): NodeMembers\NodeMembersResult
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
        return /* @lang GraphQL */ 'query NodeMembers {
          __typename
          members {
            __typename
            ... on Node {
              id
            }
          }
        }';
    }

    public static function config(): string
    {
        return '/home/bfranke/projects/sailor/examples/polymorphic/sailor.php';
    }

    public static function endpoint(): string
    {
        return 'polymorphic';
    }
}
