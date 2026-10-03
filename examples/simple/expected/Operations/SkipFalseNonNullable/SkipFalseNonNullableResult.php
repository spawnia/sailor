<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\SkipFalseNonNullable;

class SkipFalseNonNullableResult extends \Spawnia\Sailor\Result
{
    public ?SkipFalseNonNullable $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = SkipFalseNonNullable::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(SkipFalseNonNullable $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): SkipFalseNonNullableErrorFreeResult
    {
        return SkipFalseNonNullableErrorFreeResult::fromResult($this);
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
