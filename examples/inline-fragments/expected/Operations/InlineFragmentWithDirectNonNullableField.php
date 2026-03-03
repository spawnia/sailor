<?php declare(strict_types=1);

namespace Spawnia\Sailor\InlineFragments\Operations;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\InlineFragments\Operations\InlineFragmentWithDirectNonNullableField\InlineFragmentWithDirectNonNullableFieldResult>
 */
class InlineFragmentWithDirectNonNullableField extends \Spawnia\Sailor\Operation
{
    /**
     * @param bool $skip
     */
    public static function execute(
        $skip,
    ): InlineFragmentWithDirectNonNullableField\InlineFragmentWithDirectNonNullableFieldResult {
        return self::executeOperation(
            $skip,
        );
    }

    protected static function converters(): array
    {
        /** @var array<int, array{string, \Spawnia\Sailor\Convert\TypeConverter}>|null $converters */
        static $converters;

        return $converters ??= [
            ['skip', new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\BooleanConverter)],
        ];
    }

    public static function document(): string
    {
        return /* @lang GraphQL */ 'query InlineFragmentWithDirectNonNullableField($skip: Boolean!) {
          __typename
          search(query: "test") {
            __typename
            ... on Article @skip(if: $skip) {
              title
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
