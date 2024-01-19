<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\SkipNonNullable;

class SkipNonNullableResult extends \Spawnia\Sailor\Result
{
    public ?SkipNonNullable $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = SkipNonNullable::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(SkipNonNullable $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): SkipNonNullableErrorFreeResult
    {
        return SkipNonNullableErrorFreeResult::fromResult($this);
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
