<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\TakeSomeInput;

class TakeSomeInputResult extends \Spawnia\Sailor\Result
{
    public ?TakeSomeInput $data;

    protected function setData(\stdClass $data): void
    {
        $this->data = TakeSomeInput::fromStdClass($data);
    }

    public function errorFree(): TakeSomeInputErrorFreeResult
    {
        return TakeSomeInputErrorFreeResult::fromResult($this);
    }
}
