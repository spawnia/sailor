<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\SkipAndIncludeNonNullable;

class SkipAndIncludeNonNullableResult extends \Spawnia\Sailor\Result
{
    public ?SkipAndIncludeNonNullable $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = SkipAndIncludeNonNullable::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(SkipAndIncludeNonNullable $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): SkipAndIncludeNonNullableErrorFreeResult
    {
        return SkipAndIncludeNonNullableErrorFreeResult::fromResult($this);
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
