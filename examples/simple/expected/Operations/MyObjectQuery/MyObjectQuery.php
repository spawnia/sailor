<?php

declare(strict_types=1);

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
    public static function make($singleObject = 1.7976931348623157E+308): self
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
}
