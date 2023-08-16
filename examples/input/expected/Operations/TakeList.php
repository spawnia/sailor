<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Input\Operations;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\Input\Operations\TakeList\TakeListResult>
 */
class TakeList extends \Spawnia\Sailor\Operation
{
    /**
     * @param array<int|null>|null $values
     */
    public static function execute(
        $values = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): TakeList\TakeListResult
    {
        return self::executeOperation(
            $values,
        );
    }

    protected static function converters(): array
    {
        static $converters;

        return $converters ??= [
            ['values', new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter)))],
        ];
    }

    public static function document(): string
    {
        return /* @lang GraphQL */ 'mutation TakeList($values: [Int]) {
          __typename
          takeList(values: $values)
        }';
    }

    public static function endpoint(): string
    {
        return 'input';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../sailor.php');
    }
}
