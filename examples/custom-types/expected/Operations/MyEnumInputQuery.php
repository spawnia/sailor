<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\CustomTypes\Operations\MyEnumInputQuery\MyEnumInputQueryResult>
 */
class MyEnumInputQuery extends \Spawnia\Sailor\Operation
{
    /**
     * @param \Spawnia\Sailor\CustomTypes\Types\EnumInput|null $input
     */
    public static function execute($input = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.'): MyEnumInputQuery\MyEnumInputQueryResult
    {
        return self::executeOperation(
            $input,
        );
    }

    protected static function converters(): array
    {
        static $converters;

        return $converters ??= [
            ['input', new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\CustomTypes\Types\EnumInput)],
        ];
    }

    public static function document(): string
    {
        return /* @lang GraphQL */ 'query MyEnumInputQuery($input: EnumInput) {
          __typename
          withEnumInput(input: $input) {
            __typename
            custom
            default
          }
        }';
    }

    public static function config(): string
    {
        return '/home/bfranke/projects/sailor/tests/Integration/../../examples/custom-types/sailor.php';
    }

    public static function endpoint(): string
    {
        return 'custom-types';
    }
}
