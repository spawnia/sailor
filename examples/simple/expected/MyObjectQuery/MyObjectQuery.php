<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\MyObjectQuery;

class MyObjectQuery extends \Spawnia\Sailor\TypedObject
{
    /** @var \Spawnia\Sailor\Simple\MyObjectQuery\SingleObject\SomeObject|null */
    public $singleObject;

    public function singleObjectTypeMapper(): callable
    {
        return static function (\stdClass $value): \Spawnia\Sailor\TypedObject {
            return \Spawnia\Sailor\Simple\MyObjectQuery\SingleObject\SomeObject::fromStdClass($value);
        };
    }
}
