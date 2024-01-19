<?php declare(strict_types=1);

namespace Spawnia\Sailor\Convert;

class BooleanConverter implements TypeConverter
{
    public function fromGraphQL($value): bool
    {
        return $this->toBool($value);
    }

    public function toGraphQL($value): bool
    {
        return $this->toBool($value);
    }

    /** @param mixed $value Should be bool */
    protected function toBool($value): bool
    {
        if (! is_bool($value)) {
            $notBool = gettype($value);
            throw new \InvalidArgumentException("Expected bool, got {$notBool}");
        }

        return $value;
    }
}
