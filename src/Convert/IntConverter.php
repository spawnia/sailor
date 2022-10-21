<?php declare(strict_types=1);

namespace Spawnia\Sailor\Convert;

class IntConverter implements TypeConverter
{
    public function fromGraphQL($value): int
    {
        return $this->toInt($value);
    }

    public function toGraphQL($value): int
    {
        return $this->toInt($value);
    }

    /**
     * @param mixed $value Should be int
     */
    protected function toInt($value): int
    {
        if (! is_int($value)) {
            $notInt = gettype($value);
            throw new \InvalidArgumentException("Expected int, got {$notInt}");
        }

        return $value;
    }
}
