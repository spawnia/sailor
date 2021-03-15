<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\TwoArgs;

class TwoArgsResult extends \Spawnia\Sailor\Result
{
    /** @var TwoArgs|null */
    public $data;

    protected function setData(\stdClass $data): void
    {
        $this->data = TwoArgs::fromStdClass($data);
    }
}
