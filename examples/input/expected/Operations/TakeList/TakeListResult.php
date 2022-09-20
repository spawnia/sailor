<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Input\Operations\TakeList;

class TakeListResult extends \Spawnia\Sailor\Result
{
    public ?TakeList $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = TakeList::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(TakeList $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): TakeListErrorFreeResult
    {
        return TakeListErrorFreeResult::fromResult($this);
    }

    public static function endpoint(): string
    {
        return 'input';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../sailor.php');
    }
}
