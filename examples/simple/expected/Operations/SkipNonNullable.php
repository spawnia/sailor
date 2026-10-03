<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\Simple\Operations\SkipNonNullable\SkipNonNullableResult>
 */
class SkipNonNullable extends \Spawnia\Sailor\Operation
{
    /**
     * @param bool $skip
     */
    public static function execute($skip): SkipNonNullable\SkipNonNullableResult
    {
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
        return /* @lang GraphQL */ 'query SkipNonNullable($skip: Boolean!) {
          __typename
          nonNullable @skip(if: $skip)
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
