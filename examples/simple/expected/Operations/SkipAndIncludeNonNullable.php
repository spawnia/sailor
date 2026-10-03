<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\Simple\Operations\SkipAndIncludeNonNullable\SkipAndIncludeNonNullableResult>
 */
class SkipAndIncludeNonNullable extends \Spawnia\Sailor\Operation
{
    /**
     * @param bool $skip
     * @param bool $include
     */
    public static function execute($skip, $include): SkipAndIncludeNonNullable\SkipAndIncludeNonNullableResult
    {
        return self::executeOperation(
            $skip,
            $include,
        );
    }

    protected static function converters(): array
    {
        /** @var array<int, array{string, \Spawnia\Sailor\Convert\TypeConverter}>|null $converters */
        static $converters;

        return $converters ??= [
            ['skip', new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\BooleanConverter)],
            ['include', new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\BooleanConverter)],
        ];
    }

    public static function document(): string
    {
        return /* @lang GraphQL */ 'query SkipAndIncludeNonNullable($skip: Boolean!, $include: Boolean!) {
          __typename
          nonNullable @skip(if: $skip)
          ... on Query @include(if: $include) {
            nonNullable
          }
        }';
    }

    public static function endpoint(): string
    {
        return 'simple';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../sailor.php');
    }
}
