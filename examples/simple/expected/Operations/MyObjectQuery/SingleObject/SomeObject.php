<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\MyObjectQuery\SingleObject;

/**
 * @property string $__typename
 * @property int|null $value
 */
class SomeObject extends \Spawnia\Sailor\Type\TypedObject
{
    /**
     * @param int|null $value
     */
    public static function make(?int $value = null): self
    {
        $instance = new self;

        $instance->__typename = 'SomeObject';
        $instance->value = $value;

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'value' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
        ];
    }
}
