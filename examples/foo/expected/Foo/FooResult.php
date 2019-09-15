<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Foo\Foo;

class FooResult extends \Spawnia\Sailor\Result
{
    /** @var Foo|null */
    public $data;

    protected function setData(\stdClass $data): void
    {
        $this->data = Foo::fromStdClass($data);
    }
}
