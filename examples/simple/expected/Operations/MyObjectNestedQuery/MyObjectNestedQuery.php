<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\MyObjectNestedQuery;

/**
 * @property string $__typename
 * @property \Spawnia\Sailor\Simple\Operations\MyObjectNestedQuery\SingleObject\SomeObject|null $singleObject
 */
class MyObjectNestedQuery extends \Spawnia\Sailor\Type\TypedObject
{
    /**
     * @param \Spawnia\Sailor\Simple\Operations\MyObjectNestedQuery\SingleObject\SomeObject|null $singleObject
     */
    public static function make(?SingleObject\SomeObject $singleObject = null): self
    {
        $instance = new self;

        $instance->__typename = 'Query';
        $instance->singleObject = $singleObject;

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'singleObject' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Simple\Operations\MyObjectNestedQuery\SingleObject\SomeObject),
        ];
    }
}
