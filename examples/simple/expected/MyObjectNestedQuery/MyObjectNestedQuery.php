<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\MyObjectNestedQuery;

class MyObjectNestedQuery extends \Spawnia\Sailor\TypedObject
{
    /** @var string */
    public $__typename;

    /** @var \Spawnia\Sailor\Simple\MyObjectNestedQuery\SingleObject\SomeObject|null */
    public $singleObject;

    public function __typenameTypeMapper(): \Spawnia\Sailor\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\TypeConverter\NullConverter(new \Spawnia\Sailor\TypeConverter\NonNullConverter(new \Spawnia\Sailor\TypeConverter\StringConverter));
    }

    public function singleObjectTypeMapper(): \Spawnia\Sailor\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\TypeConverter\NullConverter(new \Spawnia\Sailor\Simple\MyObjectNestedQuery\SingleObject\SomeObject);
    }
}
