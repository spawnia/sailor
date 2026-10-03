<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\SkipMultipleNonNullableInlineFragment;

class SkipMultipleNonNullableInlineFragmentResult extends \Spawnia\Sailor\Result
{
    public ?SkipMultipleNonNullableInlineFragment $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = SkipMultipleNonNullableInlineFragment::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(SkipMultipleNonNullableInlineFragment $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): SkipMultipleNonNullableInlineFragmentErrorFreeResult
    {
        return SkipMultipleNonNullableInlineFragmentErrorFreeResult::fromResult($this);
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
