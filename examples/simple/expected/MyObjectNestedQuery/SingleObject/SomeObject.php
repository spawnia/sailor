<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\MyObjectNestedQuery\SingleObject;

class SomeObject extends \Spawnia\Sailor\TypedObject
{
    /** @var \Spawnia\Sailor\Simple\MyObjectNestedQuery\SingleObject\Nested\SomeObject|null */
    public $nested;

    public function nestedTypeMapper(): callable
    {
        return static function (\stdClass $value): \Spawnia\Sailor\TypedObject {
            return \Spawnia\Sailor\Simple\MyObjectNestedQuery\SingleObject\Nested\SomeObject::fromStdClass($value);
        };
    }
}
