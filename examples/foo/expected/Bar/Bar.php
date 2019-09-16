<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Foo\Bar;

class Bar extends \Spawnia\Sailor\TypedObject
{
    /** @var \Spawnia\Sailor\Foo\Bar\Bar\Bar|null */
    public $bar;

    public function typeBar(): callable
    {
        return function (\stdClass $value): \Spawnia\Sailor\ObjectType {
            return \Spawnia\Sailor\Foo\Bar\Bar\Bar::fromStdClass($value);
        };
    }
}
