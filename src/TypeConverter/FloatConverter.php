<?php

declare(strict_types=1);

namespace Spawnia\Sailor\TypeConverter;

use Spawnia\Sailor\TypeConverter;

class FloatConverter implements TypeConverter
{
    public function fromGraphQL($value): float
    {
        if (! is_float($value)) {
            throw new \InvalidArgumentException('Expected float, got '.gettype($value));
        }

        return $value;
    }

    public function toGraphQL($value): float
    {
        if (! is_float($value)) {
            throw new \InvalidArgumentException('Expected float, got '.gettype($value));
        }

        return $value;
    }
}
