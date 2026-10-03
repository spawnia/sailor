<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\SkipAliasedNonNullable;

class SkipAliasedNonNullableResult extends \Spawnia\Sailor\Result
{
    public ?SkipAliasedNonNullable $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = SkipAliasedNonNullable::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(SkipAliasedNonNullable $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): SkipAliasedNonNullableErrorFreeResult
    {
        return SkipAliasedNonNullableErrorFreeResult::fromResult($this);
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
