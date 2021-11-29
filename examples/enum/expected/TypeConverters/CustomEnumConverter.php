<?php

namespace Spawnia\Sailor\Enum\TypeConverters;

class CustomEnumConverter implements \Spawnia\Sailor\TypeConverter
{
    public function fromGraphQL($value): \Spawnia\Sailor\Enum\Enums\CustomEnum
    {
        return new \Spawnia\Sailor\Enum\Enums\CustomEnum($value);
    }

    public function toGraphQL($value)
    {
        if (! $value instanceof \Spawnia\Sailor\Enum\Enums\CustomEnum) {
            throw new \InvalidArgumentException('Expected instanceof Enum, got: ' . gettype($value));
        }

        return $value->value;
    }
}
