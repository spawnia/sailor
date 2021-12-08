<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Input\Operations\TakeSomeInput;

class TakeSomeInputResult extends \Spawnia\Sailor\Result
{
    public ?TakeSomeInput $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = TakeSomeInput::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(TakeSomeInput $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): TakeSomeInputErrorFreeResult
    {
        return TakeSomeInputErrorFreeResult::fromResult($this);
    }
}
