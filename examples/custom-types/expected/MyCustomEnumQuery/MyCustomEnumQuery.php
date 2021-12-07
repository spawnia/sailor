<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\MyCustomEnumQuery;

class MyCustomEnumQuery extends \Spawnia\Sailor\TypedObject
{
    /** @var string */
    public $__typename;

    /** @var \Spawnia\Sailor\CustomTypes\Types\CustomEnum|null */
    public $withCustomEnum;

    public function __typenameTypeMapper(): \Spawnia\Sailor\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\TypeConverter\NullConverter(new \Spawnia\Sailor\TypeConverter\NonNullConverter(new \Spawnia\Sailor\TypeConverter\StringConverter));
    }

    public function withCustomEnumTypeMapper(): \Spawnia\Sailor\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\TypeConverter\NullConverter(new \Spawnia\Sailor\CustomTypes\TypeConverters\CustomEnum);
    }
}
