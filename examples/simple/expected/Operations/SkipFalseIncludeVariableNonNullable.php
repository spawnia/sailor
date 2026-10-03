<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\Simple\Operations\SkipFalseIncludeVariableNonNullable\SkipFalseIncludeVariableNonNullableResult>
 */
class SkipFalseIncludeVariableNonNullable extends \Spawnia\Sailor\Operation
{
    /**
     * @param bool $value
     */
    public static function execute(
        $value,
    ): SkipFalseIncludeVariableNonNullable\SkipFalseIncludeVariableNonNullableResult {
        return self::executeOperation(
            $value,
        );
    }

    protected static function converters(): array
    {
        /** @var array<int, array{string, \Spawnia\Sailor\Convert\TypeConverter}>|null $converters */
        static $converters;

        return $converters ??= [
            ['value', new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\BooleanConverter)],
        ];
    }

    public static function document(): string
    {
        return /* @lang GraphQL */ 'query SkipFalseIncludeVariableNonNullable($value: Boolean!) {
          __typename
          nonNullable @skip(if: false) @include(if: $value)
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
