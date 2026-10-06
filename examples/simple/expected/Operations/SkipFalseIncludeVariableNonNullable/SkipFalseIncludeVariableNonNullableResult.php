<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\SkipFalseIncludeVariableNonNullable;

class SkipFalseIncludeVariableNonNullableResult extends \Spawnia\Sailor\Result
{
    public ?SkipFalseIncludeVariableNonNullable $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = SkipFalseIncludeVariableNonNullable::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(SkipFalseIncludeVariableNonNullable $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): SkipFalseIncludeVariableNonNullableErrorFreeResult
    {
        return SkipFalseIncludeVariableNonNullableErrorFreeResult::fromResult($this);
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
