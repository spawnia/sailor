<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\TypeConverters;

class CustomEnum implements \Spawnia\Sailor\TypeConverter
{
    public function fromGraphQL($value): \Spawnia\Sailor\CustomTypes\Types\CustomEnum
    {
        return new \Spawnia\Sailor\CustomTypes\Types\CustomEnum($value);
    }

    public function toGraphQL($value)
    {
        if (! $value instanceof \Spawnia\Sailor\CustomTypes\Types\CustomEnum) {
            throw new \InvalidArgumentException('Expected instanceof Enum, got: '.gettype($value));
        }

        return $value->value;
    }
}
