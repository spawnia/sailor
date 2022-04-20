<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\Simple\Operations\TwoArgs\TwoArgsResult>
 */
class TwoArgs extends \Spawnia\Sailor\Operation
{
    /**
     * @param string|null $first
     * @param int|null $second
     */
    public static function execute(
        $first = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $second = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.'
    ): TwoArgs\TwoArgsResult {
        return self::executeOperation(
            $first,
            $second,
        );
    }

    protected static function converters(): array
    {
        static $converters;

        return $converters ??= [
            ['first', new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter)],
            ['second', new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter)],
        ];
    }

    public static function document(): string
    {
        return /* @lang GraphQL */ 'query TwoArgs($first: String, $second: Int) {
          __typename
          twoArgs(first: $first, second: $second)
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
