<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\CustomTypes\Operations\MyNativeEnumQuery\MyNativeEnumQueryResult>
 */
class MyNativeEnumQuery extends \Spawnia\Sailor\Operation
{
    /**
     * @param \Spawnia\Sailor\CustomTypes\Types\NativeEnum|null $value
     */
    public static function execute(
        $value = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): MyNativeEnumQuery\MyNativeEnumQueryResult
    {
        return self::executeOperation(
            $value,
        );
    }

    protected static function converters(): array
    {
        static $converters;

        return $converters ??= [
            ['value', new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\CustomTypes\TypeConverters\NativeEnumConverter)],
        ];
    }

    public static function document(): string
    {
        return /* @lang GraphQL */ 'query MyNativeEnumQuery($value: NativeEnum) {
          __typename
          withNativeEnum(value: $value)
        }';
    }

    public static function endpoint(): string
    {
        return 'custom-types';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../sailor.php');
    }
}
