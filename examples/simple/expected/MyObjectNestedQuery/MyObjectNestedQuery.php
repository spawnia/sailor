<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\MyObjectNestedQuery;

class MyObjectNestedQuery extends \Spawnia\Sailor\TypedObject
{
    /** @var \Spawnia\Sailor\Simple\MyObjectNestedQuery\SingleObject\SomeObject|null */
    public $singleObject;

    public function singleObjectTypeMapper(): callable
    {
        return static function (\stdClass $value): \Spawnia\Sailor\TypedObject {
            return \Spawnia\Sailor\Simple\MyObjectNestedQuery\SingleObject\SomeObject::fromStdClass($value);
        };
    }
}
