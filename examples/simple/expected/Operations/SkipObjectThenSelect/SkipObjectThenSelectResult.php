<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\SkipObjectThenSelect;

class SkipObjectThenSelectResult extends \Spawnia\Sailor\Result
{
    public ?SkipObjectThenSelect $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = SkipObjectThenSelect::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(SkipObjectThenSelect $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): SkipObjectThenSelectErrorFreeResult
    {
        return SkipObjectThenSelectErrorFreeResult::fromResult($this);
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
