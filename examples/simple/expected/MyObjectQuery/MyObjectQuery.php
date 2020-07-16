<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\MyObjectQuery;

class MyObjectQuery extends \Spawnia\Sailor\TypedObject
{
    /** @var \Spawnia\Sailor\Simple\MyObjectQuery\SingleObject\SingleObject|null */
    public $singleObject;

    public function typeSingleObject(): callable
    {
        return static function (\stdClass $value): \Spawnia\Sailor\TypedObject {
            return \Spawnia\Sailor\Simple\MyObjectQuery\SingleObject\SingleObject::fromStdClass($value);
        };
    }
}
