<?php

declare(strict_types=1);

namespace Spawnia\Sailor\TypeConverter;

use Spawnia\Sailor\TypeConverter;

class BooleanConverter implements TypeConverter
{
    public function fromGraphQL($value): bool
    {
        if (! is_bool($value)) {
            throw new \InvalidArgumentException('Expected bool, got ' . gettype($value));
        }

        return $value;
    }

    public function toGraphQL($value): bool
    {
        if (! is_bool($value)) {
            throw new \InvalidArgumentException('Expected bool, got ' . gettype($value));
        }

        return $value;
    }
}
