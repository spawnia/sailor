<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations\MyEnumInputQuery;

/**
 * @property string $__typename
 * @property \Spawnia\Sailor\CustomTypes\Operations\MyEnumInputQuery\WithEnumInput\EnumObject|null $withEnumInput
 */
class MyEnumInputQuery extends \Spawnia\Sailor\Type\TypedObject
{
    /**
     * @param \Spawnia\Sailor\CustomTypes\Operations\MyEnumInputQuery\WithEnumInput\EnumObject|null $withEnumInput
     */
    public static function make(?WithEnumInput\EnumObject $withEnumInput = null): self
    {
        $instance = new self;

        $instance->__typename = 'Query';
        $instance->withEnumInput = $withEnumInput;

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'withEnumInput' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\CustomTypes\Operations\MyEnumInputQuery\WithEnumInput\EnumObject),
        ];
    }
}
