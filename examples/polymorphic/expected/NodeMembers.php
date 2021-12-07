<?php declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\Polymorphic\NodeMembers\NodeMembersResult>
 */
class NodeMembers extends \Spawnia\Sailor\Operation
{
    public static function execute(): NodeMembers\NodeMembersResult
    {
        return self::executeOperation(...func_get_args());
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

    public static function endpoint(): string
    {
        return 'polymorphic';
    }
}
