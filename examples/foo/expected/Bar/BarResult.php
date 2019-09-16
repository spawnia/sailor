<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Foo\Bar;

class BarResult extends \Spawnia\Sailor\Result
{
    /** @var Bar|null */
    public $data;

    protected function setData(\stdClass $data): void
    {
        $this->data = Bar::fromStdClass($data);
    }
}
