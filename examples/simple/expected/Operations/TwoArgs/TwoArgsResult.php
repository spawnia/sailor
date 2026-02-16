<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\TwoArgs;

class TwoArgsResult extends \Spawnia\Sailor\Result
{
    public ?TwoArgs $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = TwoArgs::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(TwoArgs $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): TwoArgsErrorFreeResult
    {
        return TwoArgsErrorFreeResult::fromResult($this);
    }

    public static function endpoint(): string
    {
        return 'simple';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../sailor.php');
    }
}
