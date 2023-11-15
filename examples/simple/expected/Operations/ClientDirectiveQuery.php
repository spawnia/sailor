<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\Simple\Operations\ClientDirectiveQuery\ClientDirectiveQueryResult>
 */
class ClientDirectiveQuery extends \Spawnia\Sailor\Operation
{
    /**
     * @param bool $value
     */
    public static function execute($value): ClientDirectiveQuery\ClientDirectiveQueryResult
    {
        return self::executeOperation(
            $value,
        );
    }

    protected static function converters(): array
    {
        static $converters;

        return $converters ??= [
            ['value', new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\BooleanConverter)],
        ];
    }

    public static function document(): string
    {
        return /* @lang GraphQL */ 'query ClientDirectiveQuery($value: Boolean!) {
          __typename
          scalarWithArg @skip(if: true)
          twoArgs @include(if: $value)
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
