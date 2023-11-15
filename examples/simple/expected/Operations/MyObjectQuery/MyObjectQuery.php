<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\MyObjectQuery;

/**
 * @property string $__typename
 * @property \Spawnia\Sailor\Simple\Operations\MyObjectQuery\SingleObject\SomeObject|null $singleObject
 */
class MyObjectQuery extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \Spawnia\Sailor\Simple\Operations\MyObjectQuery\SingleObject\SomeObject|null $singleObject
     */
    public static function make($singleObject = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.'): self
    {
        $instance = new self;

        $instance->__typename = 'Query';
        if ($singleObject !== self::UNDEFINED) {
            $instance->singleObject = $singleObject;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'singleObject' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Simple\Operations\MyObjectQuery\SingleObject\SomeObject),
        ];
    }

    public static function endpoint(): string
    {
        return 'simple';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../sailor.php');
    }
}
