<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\MyObjectNestedQuery\SingleObject\Nested;

/**
 * @property string $__typename
 * @property int|null $value
 */
class SomeObject extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|null $value
     */
    public static function make($value = 1.7976931348623157E+308): self
    {
        $instance = new self;

        $instance->__typename = 'SomeObject';
        if ($value !== self::UNDEFINED) {
            $instance->value = $value;
        }

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
