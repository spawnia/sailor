<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\SkipNonNullableFragmentSpread;

class SkipNonNullableFragmentSpreadResult extends \Spawnia\Sailor\Result
{
    public ?SkipNonNullableFragmentSpread $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = SkipNonNullableFragmentSpread::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(SkipNonNullableFragmentSpread $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): SkipNonNullableFragmentSpreadErrorFreeResult
    {
        return SkipNonNullableFragmentSpreadErrorFreeResult::fromResult($this);
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
