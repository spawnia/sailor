<?php declare(strict_types=1);

namespace Spawnia\Sailor\Convert;

class FloatConverter implements TypeConverter
{
    public function fromGraphQL($value): float
    {
        // JSON floats can appear like int's
        if (is_int($value)) {
            $value = (float) $value;
        }

        if (! is_float($value)) {
            throw new \InvalidArgumentException('Expected float, got ' . gettype($value));
        }

        return $value;
    }

    public function toGraphQL($value): float
    {
        if (! is_float($value)) {
            throw new \InvalidArgumentException('Expected float, got ' . gettype($value));
        }

        return $value;
    }
}
