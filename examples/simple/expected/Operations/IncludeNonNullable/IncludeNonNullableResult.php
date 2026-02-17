<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\IncludeNonNullable;

class IncludeNonNullableResult extends \Spawnia\Sailor\Result
{
    public ?IncludeNonNullable $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = IncludeNonNullable::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(IncludeNonNullable $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): IncludeNonNullableErrorFreeResult
    {
        return IncludeNonNullableErrorFreeResult::fromResult($this);
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
