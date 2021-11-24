<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic;

class NodeMembers extends \Spawnia\Sailor\Operation
{
    public static function execute(): NodeMembers\NodeMembersResult
    {
        return self::executeOperation(...func_get_args());
    }

    public static function document(): string
    {
        return /* @lang GraphQL */ 'query NodeMembers {
          members {
            ... on Node {
              id
            }
            __typename
          }
          __typename
        }';
    }

    public static function endpoint(): string
    {
        return 'polymorphic';
    }
}
