<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\MyObjectNestedQuery\SingleObject;

class SomeObject extends \Spawnia\Sailor\TypedObject
{
    /** @var string */
    public $__typename;

    /** @var \Spawnia\Sailor\Simple\Operations\MyObjectNestedQuery\SingleObject\Nested\SomeObject|null */
    public $nested;

    public function __typenameTypeMapper(): \Spawnia\Sailor\Convert\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter));
    }

    public function nestedTypeMapper(): \Spawnia\Sailor\Convert\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Simple\Operations\MyObjectNestedQuery\SingleObject\Nested\SomeObject);
    }
}
