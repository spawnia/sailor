<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\MyObjectNestedQuery;

class MyObjectNestedQuery extends \Spawnia\Sailor\TypedObject
{
    /** @var string */
    public $__typename;

    /** @var \Spawnia\Sailor\Simple\Operations\MyObjectNestedQuery\SingleObject\SomeObject|null */
    public $singleObject;

    public function __typenameTypeMapper(): \Spawnia\Sailor\Convert\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter));
    }

    public function singleObjectTypeMapper(): \Spawnia\Sailor\Convert\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Simple\Operations\MyObjectNestedQuery\SingleObject\SomeObject);
    }
}
