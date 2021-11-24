<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\Polymorphic\AllMembers\AllMembersResult>
 */
class AllMembers extends \Spawnia\Sailor\Operation
{
    public static function execute(): AllMembers\AllMembersResult
    {
        return self::executeOperation(...func_get_args());
    }

    public static function document(): string
    {
        return /* @lang GraphQL */ 'query AllMembers {
          members {
            ... on User {
              name
            }
            ... on Organization {
              code
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
