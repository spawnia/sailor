<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic;

class UserOrPost extends \Spawnia\Sailor\Operation
{
    public static function execute(?string $first = null, ?int $second = null): UserOrPost\UserOrPostResult
    {
        return self::executeOperation(...func_get_args());
    }

    public static function document(): string
    {
        return /* @lang GraphQL */ 'query UserOrPost($id: ID!) {
    node(id: $id) {
        id
        ... on User {
            name
        }
        ... on Post {
            title
        }
    }
}';
    }

    public static function endpoint(): string
    {
        return 'polymorphic';
    }
}
