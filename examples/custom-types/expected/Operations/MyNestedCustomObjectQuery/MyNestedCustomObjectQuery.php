<?php declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations\MyNestedCustomObjectQuery;

/**
 * @property string $__typename
 * @property \Spawnia\Sailor\CustomTypes\Operations\MyNestedCustomObjectQuery\WithNestedCustomObject\NestedCustomObject|null $withNestedCustomObject
 */
class MyNestedCustomObjectQuery extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \Spawnia\Sailor\CustomTypes\Operations\MyNestedCustomObjectQuery\WithNestedCustomObject\NestedCustomObject|null $withNestedCustomObject
     */
    public static function make(
        $withNestedCustomObject = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        $instance->__typename = 'Query';
        if ($withNestedCustomObject !== self::UNDEFINED) {
            $instance->withNestedCustomObject = $withNestedCustomObject;
        }

        return $instance;
    }

    protected function converters(): array
    {
        /** @var array<string, \Spawnia\Sailor\Convert\TypeConverter>|null $converters */
        static $converters;

        return $converters ??= [
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'withNestedCustomObject' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\CustomTypes\Operations\MyNestedCustomObjectQuery\WithNestedCustomObject\NestedCustomObject),
        ];
    }

    public static function endpoint(): string
    {
        return 'custom-types';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../sailor.php');
    }
}
