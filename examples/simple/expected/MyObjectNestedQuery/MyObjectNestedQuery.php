<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\MyObjectNestedQuery;

class MyObjectNestedQuery extends \Spawnia\Sailor\TypedObject
{
    /** @var \Spawnia\Sailor\Simple\MyObjectNestedQuery\SingleObject\SingleObject|null */
    public $singleObject;

    public function typeSingleObject(): callable
    {
        return function (\stdClass $value): \Spawnia\Sailor\TypedObject {
            return \Spawnia\Sailor\Simple\MyObjectNestedQuery\SingleObject\SingleObject::fromStdClass($value);
        };
    }
}
