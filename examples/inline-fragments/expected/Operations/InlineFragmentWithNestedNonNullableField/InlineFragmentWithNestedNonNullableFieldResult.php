<?php declare(strict_types=1);

namespace Spawnia\Sailor\InlineFragments\Operations\InlineFragmentWithNestedNonNullableField;

class InlineFragmentWithNestedNonNullableFieldResult extends \Spawnia\Sailor\Result
{
    public ?InlineFragmentWithNestedNonNullableField $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = InlineFragmentWithNestedNonNullableField::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(InlineFragmentWithNestedNonNullableField $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): InlineFragmentWithNestedNonNullableFieldErrorFreeResult
    {
        return InlineFragmentWithNestedNonNullableFieldErrorFreeResult::fromResult($this);
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
