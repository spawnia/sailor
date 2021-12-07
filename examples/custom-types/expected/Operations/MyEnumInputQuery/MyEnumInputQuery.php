<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations\MyEnumInputQuery;

/**
 * @property string $__typename
 * @property \Spawnia\Sailor\CustomTypes\Operations\MyEnumInputQuery\WithEnumInput\EnumObject|null $withEnumInput
 */
class MyEnumInputQuery extends \Spawnia\Sailor\Type\TypedObject
{
    /** @var string */
    public $__typename;

    /** @var \Spawnia\Sailor\CustomTypes\Operations\MyEnumInputQuery\WithEnumInput\EnumObject|null */
    public $withEnumInput;

    public function converters(): array
    {
        static $converters;

        return $converters ??= [
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'withEnumInput' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\CustomTypes\Operations\MyEnumInputQuery\WithEnumInput\EnumObject),
        ];
    }

    public function __typenameTypeMapper(): \Spawnia\Sailor\Convert\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter);
    }

    public function withEnumInputTypeMapper(): \Spawnia\Sailor\Convert\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\CustomTypes\Operations\MyEnumInputQuery\WithEnumInput\EnumObject);
    }
}
