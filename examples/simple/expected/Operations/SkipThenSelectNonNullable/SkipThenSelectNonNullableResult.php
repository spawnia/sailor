<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\SkipThenSelectNonNullable;

class SkipThenSelectNonNullableResult extends \Spawnia\Sailor\Result
{
    public ?SkipThenSelectNonNullable $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = SkipThenSelectNonNullable::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(SkipThenSelectNonNullable $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): SkipThenSelectNonNullableErrorFreeResult
    {
        return SkipThenSelectNonNullableErrorFreeResult::fromResult($this);
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
