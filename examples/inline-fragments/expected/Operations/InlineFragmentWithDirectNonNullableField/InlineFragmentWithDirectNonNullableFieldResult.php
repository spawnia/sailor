<?php declare(strict_types=1);

namespace Spawnia\Sailor\InlineFragments\Operations\InlineFragmentWithDirectNonNullableField;

class InlineFragmentWithDirectNonNullableFieldResult extends \Spawnia\Sailor\Result
{
    public ?InlineFragmentWithDirectNonNullableField $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = InlineFragmentWithDirectNonNullableField::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(InlineFragmentWithDirectNonNullableField $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): InlineFragmentWithDirectNonNullableFieldErrorFreeResult
    {
        return InlineFragmentWithDirectNonNullableFieldErrorFreeResult::fromResult($this);
    }

    public static function endpoint(): string
    {
        return 'inline-fragments';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../sailor.php');
    }
}
