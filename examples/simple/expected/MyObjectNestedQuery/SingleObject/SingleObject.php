<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\MyObjectNestedQuery\SingleObject;

class SingleObject extends \Spawnia\Sailor\TypedObject
{
    /** @var \Spawnia\Sailor\Simple\MyObjectNestedQuery\SingleObject\Nested\Nested|null */
    public $nested;

    public function typeNested(): callable
    {
        return static function (\stdClass $value): \Spawnia\Sailor\TypedObject {
            return \Spawnia\Sailor\Simple\MyObjectNestedQuery\SingleObject\Nested\Nested::fromStdClass($value);
        };
    }
}
