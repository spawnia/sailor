<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Foo\Foo;

class Foo extends \Spawnia\Sailor\TypedObject
{
    /** @var string|null */
    public $foo;

    public function typeFoo(): callable
    {
        return new \Spawnia\Sailor\Mapper\StringMapper();
    }
}
