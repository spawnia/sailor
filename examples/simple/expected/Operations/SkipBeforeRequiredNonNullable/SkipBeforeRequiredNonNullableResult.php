<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\SkipBeforeRequiredNonNullable;

class SkipBeforeRequiredNonNullableResult extends \Spawnia\Sailor\Result
{
    public ?SkipBeforeRequiredNonNullable $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = SkipBeforeRequiredNonNullable::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(SkipBeforeRequiredNonNullable $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): SkipBeforeRequiredNonNullableErrorFreeResult
    {
        return SkipBeforeRequiredNonNullableErrorFreeResult::fromResult($this);
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
