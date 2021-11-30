<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\MyObjectNestedQuery\SingleObject;

class SomeObject extends \Spawnia\Sailor\TypedObject
{
    /** @var \Spawnia\Sailor\Simple\MyObjectNestedQuery\SingleObject\Nested\SomeObject|null */
    public $nested;

    public function nestedTypeMapper(): \Spawnia\Sailor\TypeConverter
    {
        static $converter;
        return $converter ??= new \Spawnia\Sailor\TypeConverter\NullConverter(new \Spawnia\Sailor\Simple\MyObjectNestedQuery\SingleObject\Nested\SomeObject);
    }
}
