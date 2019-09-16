<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Foo\Bar\Bar;

class Bar extends \Spawnia\Sailor\TypedObject
{
    /** @var int|null */
    public $baz;

    public function typeBaz(): callable
    {
        return new \Spawnia\Sailor\Mapper\DirectMapper();
    }
}
