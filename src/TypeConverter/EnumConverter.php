<?php

declare(strict_types=1);

namespace Spawnia\Sailor\TypeConverter;

use Spawnia\Sailor\TypeConverter;

class EnumConverter implements TypeConverter
{
    public function fromGraphQL($value): string
    {
        if (! is_string($value)) {
            throw new \InvalidArgumentException('Expected string, got '.gettype($value));
        }

        return $value;
    }

    public function toGraphQL($value): string
    {
        if (! is_string($value)) {
            throw new \InvalidArgumentException('Expected string, got '.gettype($value));
        }

        return $value;
    }
}
