<?php

declare(strict_types=1);

namespace Spawnia\Sailor\TypeConverter;

use Spawnia\Sailor\TypeConverter;

class IntConverter implements TypeConverter
{
    public function fromGraphQL($value): int
    {
        if (! is_int($value)) {
            throw new \InvalidArgumentException('Expected int, got ' . gettype($value));
        }

        return $value;
    }

    public function toGraphQL($value): int
    {
        if (! is_int($value)) {
            throw new \InvalidArgumentException('Expected int, got ' . gettype($value));
        }

        return $value;
    }
}
