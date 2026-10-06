<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\SelectThenSkipNonNullable;

class SelectThenSkipNonNullableResult extends \Spawnia\Sailor\Result
{
    public ?SelectThenSkipNonNullable $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = SelectThenSkipNonNullable::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(SelectThenSkipNonNullable $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): SelectThenSkipNonNullableErrorFreeResult
    {
        return SelectThenSkipNonNullableErrorFreeResult::fromResult($this);
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
