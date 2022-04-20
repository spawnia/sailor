<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\Simple\Operations\MyScalarQuery\MyScalarQueryResult>
 */
class MyScalarQuery extends \Spawnia\Sailor\Operation
{
    /**
     * @param string|null $arg
     */
    public static function execute($arg = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.'): MyScalarQuery\MyScalarQueryResult
    {
        return self::executeOperation(
            $arg,
        );
    }

    protected static function converters(): array
    {
        static $converters;

        return $converters ??= [
            ['arg', new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter)],
        ];
    }

    public static function document(): string
    {
        return /* @lang GraphQL */ 'query MyScalarQuery($arg: String) {
          __typename
          scalarWithArg(arg: $arg)
        }';
    }

    public static function config(): string
    {
        return '/home/bfranke/projects/sailor/examples/simple/sailor.php';
    }

    public static function endpoint(): string
    {
        return 'simple';
    }
}
