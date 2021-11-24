<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\MyObjectNestedQuery\SingleObject\Nested;

class SomeObject extends \Spawnia\Sailor\TypedObject
{
    /** @var int|null */
    public $value;

    public function valueTypeMapper(): callable
    {
        return new \Spawnia\Sailor\Mapper\DirectMapper();
    }
}
