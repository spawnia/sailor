<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\CustomTypes\Operations\MyBenSampoEnumQuery\MyBenSampoEnumQueryResult>
 */
class MyBenSampoEnumQuery extends \Spawnia\Sailor\Operation
{
    /**
     * @param \Spawnia\Sailor\CustomTypes\Types\BenSampoEnum|null $value
     */
    public static function execute($value = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.'): MyBenSampoEnumQuery\MyBenSampoEnumQueryResult
    {
        return self::executeOperation(
            $value,
        );
    }

    protected static function converters(): array
    {
        static $converters;

        return $converters ??= [
            ['value', new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\CustomTypes\TypeConverters\BenSampoEnumConverter)],
        ];
    }

    public static function document(): string
    {
        return /* @lang GraphQL */ 'query MyBenSampoEnumQuery($value: BenSampoEnum) {
          __typename
          withBenSampoEnum(value: $value)
        }';
    }

    public static function endpoint(): string
    {
        return 'custom-types';
    }

    public static function config(): string
    {
        return __DIR__ . '/../../sailor.php';
    }
}
