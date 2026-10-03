<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\SkipMultipleNonNullableFragmentSpread;

class SkipMultipleNonNullableFragmentSpreadResult extends \Spawnia\Sailor\Result
{
    public ?SkipMultipleNonNullableFragmentSpread $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = SkipMultipleNonNullableFragmentSpread::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(SkipMultipleNonNullableFragmentSpread $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): SkipMultipleNonNullableFragmentSpreadErrorFreeResult
    {
        return SkipMultipleNonNullableFragmentSpreadErrorFreeResult::fromResult($this);
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
