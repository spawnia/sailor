<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Enum\MyEnumInputQuery\WithEnumInput;

class EnumObject extends \Spawnia\Sailor\TypedObject
{
    /** @var string */
    public $__typename;

    /** @var \Spawnia\Sailor\Enum\Types\CustomEnum|null */
    public $custom;

    /** @var string|null */
    public $default;

    public function __typenameTypeMapper(): \Spawnia\Sailor\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\TypeConverter\NullConverter(new \Spawnia\Sailor\TypeConverter\NonNullConverter(new \Spawnia\Sailor\TypeConverter\StringConverter));
    }

    public function customTypeMapper(): \Spawnia\Sailor\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\TypeConverter\NullConverter(new \Spawnia\Sailor\Enum\TypeConverters\CustomEnum);
    }

    public function defaultTypeMapper(): \Spawnia\Sailor\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\TypeConverter\NullConverter(new \Spawnia\Sailor\TypeConverter\EnumConverter);
    }
}
