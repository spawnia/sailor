<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\Polymorphic\Operations\AllMembers\AllMembersResult>
 */
class AllMembers extends \Spawnia\Sailor\Operation
{
    public static function execute(): AllMembers\AllMembersResult
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
        return /* @lang GraphQL */ 'query AllMembers {
          __typename
          members {
            __typename
            ... on User {
              name
            }
            ... on Organization {
              code
            }
          }
        }';
    }

    public static function config(): string
    {
        return '/home/bfranke/projects/sailor/tests/Integration/../../examples/polymorphic/sailor.php';
    }

    public static function endpoint(): string
    {
        return 'polymorphic';
    }
}
