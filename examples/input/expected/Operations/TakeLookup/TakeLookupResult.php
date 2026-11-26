<?php declare(strict_types=1);

namespace Spawnia\Sailor\Input\Operations\TakeLookup;

class TakeLookupResult extends \Spawnia\Sailor\Result
{
    public ?TakeLookup $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = TakeLookup::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(TakeLookup $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): TakeLookupErrorFreeResult
    {
        return TakeLookupErrorFreeResult::fromResult($this);
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
