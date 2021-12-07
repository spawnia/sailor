<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\TwoArgs;

class TwoArgsResult extends \Spawnia\Sailor\Result
{
    public ?TwoArgs $data;

    protected function setData(\stdClass $data): void
    {
        $this->data = TwoArgs::fromStdClass($data);
    }

    public function errorFree(): TwoArgsErrorFreeResult
    {
        return TwoArgsErrorFreeResult::fromResult($this);
    }
}
