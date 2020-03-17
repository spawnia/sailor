<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\MyObjectNestedQuery\SingleObject\Nested;

class Nested extends \Spawnia\Sailor\TypedObject
{
    /** @var int|null */
    public $value;

    public function typeValue(): callable
    {
        return new \Spawnia\Sailor\Mapper\DirectMapper();
    }
}
