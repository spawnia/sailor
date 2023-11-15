<?php declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\CustomTypes\Operations\MyCustomObjectQuery\MyCustomObjectQueryResult>
 */
class MyCustomObjectQuery extends \Spawnia\Sailor\Operation
{
    /**
     * @param \Spawnia\Sailor\CustomTypesSrc\CustomObject|null $value
     */
    public static function execute($value = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.'): MyCustomObjectQuery\MyCustomObjectQueryResult
    {
        return self::executeOperation(
            $value,
        );
    }

    protected static function converters(): array
    {
        static $converters;

        return $converters ??= [
            ['value', new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\CustomTypes\TypeConverters\CustomInputConverter)],
        ];
    }

    public static function document(): string
    {
        return /* @lang GraphQL */ 'query MyCustomObjectQuery($value: CustomInput) {
          __typename
          withCustomObject(value: $value) {
            __typename
            foo
          }
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
