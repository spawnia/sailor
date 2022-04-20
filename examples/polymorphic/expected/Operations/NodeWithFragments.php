<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments\NodeWithFragmentsResult>
 */
class NodeWithFragments extends \Spawnia\Sailor\Operation
{
    /**
     * @param string $id
     */
    public static function execute($id): NodeWithFragments\NodeWithFragmentsResult
    {
        return self::executeOperation(
            $id,
        );
    }

    protected static function converters(): array
    {
        static $converters;

        return $converters ??= [
            ['id', new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter)],
        ];
    }

    public static function document(): string
    {
        return /* @lang GraphQL */ 'query NodeWithFragments($id: ID!) {
          __typename
          node(id: $id) {
            __typename
            node {
              __typename
              node {
                __typename
                id
                ... on User {
                  id
                }
              }
            }
            ... on Task {
              done
              node {
                __typename
                node {
                  __typename
                  ... on User {
                    name
                  }
                }
              }
            }
            ... on Post {
              id
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
