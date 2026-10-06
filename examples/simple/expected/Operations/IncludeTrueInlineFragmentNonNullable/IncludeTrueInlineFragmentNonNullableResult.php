<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\IncludeTrueInlineFragmentNonNullable;

class IncludeTrueInlineFragmentNonNullableResult extends \Spawnia\Sailor\Result
{
    public ?IncludeTrueInlineFragmentNonNullable $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = IncludeTrueInlineFragmentNonNullable::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(IncludeTrueInlineFragmentNonNullable $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): IncludeTrueInlineFragmentNonNullableErrorFreeResult
    {
        return IncludeTrueInlineFragmentNonNullableErrorFreeResult::fromResult($this);
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
