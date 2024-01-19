<?php declare(strict_types=1);

namespace Spawnia\Sailor\Convert;

class EnumConverter implements TypeConverter
{
    public function fromGraphQL($value): string
    {
        return $this->toString($value);
    }

    public function toGraphQL($value): string
    {
        return $this->toString($value);
    }

    /** @param mixed $value Should be string */
    protected function toString($value): string
    {
        if (! is_string($value)) {
            $notString = gettype($value);
            throw new \InvalidArgumentException("Expected string, got {$notString}");
        }

        return $value;
    }
}
