<?php declare(strict_types=1);

namespace Spawnia\Sailor\InlineFragments\Operations;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\InlineFragments\Operations\SearchQuery\SearchQueryResult>
 */
class SearchQuery extends \Spawnia\Sailor\Operation
{
    /**
     * @param string $query
     */
    public static function execute($query): SearchQuery\SearchQueryResult
    {
        return self::executeOperation(
            $query,
        );
    }

    protected static function converters(): array
    {
        /** @var array<int, array{string, \Spawnia\Sailor\Convert\TypeConverter}>|null $converters */
        static $converters;

        return $converters ??= [
            ['query', new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter)],
        ];
    }

    public static function document(): string
    {
        return /* @lang GraphQL */ 'query SearchQuery($query: String!) {
          __typename
          search(query: $query) {
            __typename
            id
            ... on Article {
              title
              content {
                __typename
                text
              }
            }
            ... on Video {
              title
              content {
                __typename
                url
                duration
              }
            }
          }
        }';
    }

    public static function endpoint(): string
    {
        return 'inline-fragments';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../sailor.php');
    }
}
