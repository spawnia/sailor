<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\MyObjectNestedQuery\SingleObject;

/**
 * @property string $__typename
 * @property \Spawnia\Sailor\Simple\Operations\MyObjectNestedQuery\SingleObject\Nested\SomeObject|null $nested
 */
class SomeObject extends \Spawnia\Sailor\Type\TypedObject
{
    /**
     * @param \Spawnia\Sailor\Simple\Operations\MyObjectNestedQuery\SingleObject\Nested\SomeObject|null $nested
     */
    public static function make(?Nested\SomeObject $nested = null): self
    {
        $instance = new self;

        $instance->__typename = 'SomeObject';
        $instance->nested = $nested;

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'nested' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Simple\Operations\MyObjectNestedQuery\SingleObject\Nested\SomeObject),
        ];
    }
}
