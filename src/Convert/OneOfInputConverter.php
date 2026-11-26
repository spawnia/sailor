<?php declare(strict_types=1);

namespace Spawnia\Sailor\Convert;

use Spawnia\Sailor\OneOfInput;

abstract class OneOfInputConverter implements TypeConverter
{
    /** @return array<string, class-string<OneOfInput>> */
    abstract protected function variants(): array;

    public function fromGraphQL($value): OneOfInput
    {
        if (! $value instanceof \stdClass) {
            throw new \InvalidArgumentException('Expected a OneOf input object.');
        }

        $fields = get_object_vars($value);
        $field = array_key_first($fields);
        $variants = $this->variants();
        if (count($fields) !== 1 || ! is_string($field) || ! isset($variants[$field])) {
            throw new \InvalidArgumentException('Expected exactly one field of the OneOf input object.');
        }

        return $variants[$field]::fromStdClass($value);
    }

    public function toGraphQL($value): \stdClass
    {
        if (! $value instanceof OneOfInput || ! in_array(get_class($value), $this->variants(), true)) {
            throw new \InvalidArgumentException('Expected a variant of the OneOf input object.');
        }

        return $value->toStdClass();
    }
}
