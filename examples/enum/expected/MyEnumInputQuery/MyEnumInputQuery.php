<?php declare(strict_types=1);

namespace Spawnia\Sailor\Enum\MyEnumInputQuery;

class MyEnumInputQuery extends \Spawnia\Sailor\TypedObject
{
    /** @var string */
    public $__typename;

    /** @var \Spawnia\Sailor\Enum\MyEnumInputQuery\WithEnumInput\EnumObject|null */
    public $withEnumInput;

    public function __typenameTypeMapper(): \Spawnia\Sailor\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\TypeConverter\NullConverter(new \Spawnia\Sailor\TypeConverter\NonNullConverter(new \Spawnia\Sailor\TypeConverter\StringConverter));
    }

    public function withEnumInputTypeMapper(): \Spawnia\Sailor\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\TypeConverter\NullConverter(new \Spawnia\Sailor\Enum\MyEnumInputQuery\WithEnumInput\EnumObject);
    }
}
