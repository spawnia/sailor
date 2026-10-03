<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\Simple\Operations\IncludeFalseNonNullable\IncludeFalseNonNullableResult>
 */
class IncludeFalseNonNullable extends \Spawnia\Sailor\Operation
{
    public static function execute(): IncludeFalseNonNullable\IncludeFalseNonNullableResult
    {
        return self::executeOperation(
        );
    }

    protected static function converters(): array
    {
        /** @var array<int, array{string, \Spawnia\Sailor\Convert\TypeConverter}>|null $converters */
        static $converters;

        return $converters ??= [
        ];
    }

    public static function document(): string
    {
        return /* @lang GraphQL */ 'query IncludeFalseNonNullable {
          __typename
          nonNullable @include(if: false)
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
