<?php declare(strict_types=1);

namespace Spawnia\Sailor\Convert;

class FloatConverter implements TypeConverter
{
    public function fromGraphQL($value): float
    {
        return $this->toFloat($value);
    }

    public function toGraphQL($value): float
    {
        return $this->toFloat($value);
    }

    /**
     * @param mixed $value Should be float
     */
    protected function toFloat($value): float
    {
        // JSON floats can appear like ints
        if (is_int($value)) {
            $value = (float) $value;
        }

        if (is_float($value)) {
            return $value;
        }

        $notFloat = gettype($value);
        throw new \InvalidArgumentException("Expected float, got {$notFloat}");
    }
}
