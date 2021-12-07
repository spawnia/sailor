<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Enum\TypeConverters;

class CustomEnum implements \Spawnia\Sailor\TypeConverter
{
    public function fromGraphQL($value): \Spawnia\Sailor\Enum\Types\CustomEnum
    {
        return new \Spawnia\Sailor\Enum\Types\CustomEnum($value);
    }

    public function toGraphQL($value)
    {
        if (! $value instanceof \Spawnia\Sailor\Enum\Types\CustomEnum) {
            throw new \InvalidArgumentException('Expected instanceof Enum, got: '.gettype($value));
        }

        return $value->value;
    }
}
