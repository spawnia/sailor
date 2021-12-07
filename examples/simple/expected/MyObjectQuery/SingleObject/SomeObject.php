<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\MyObjectQuery\SingleObject;

class SomeObject extends \Spawnia\Sailor\TypedObject
{
    /** @var string */
    public $__typename;

    /** @var int|null */
    public $value;

    public function __typenameTypeMapper(): \Spawnia\Sailor\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\TypeConverter\NullConverter(new \Spawnia\Sailor\TypeConverter\NonNullConverter(new \Spawnia\Sailor\TypeConverter\StringConverter));
    }

    public function valueTypeMapper(): \Spawnia\Sailor\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\TypeConverter\NullConverter(new \Spawnia\Sailor\TypeConverter\IntConverter);
    }
}
