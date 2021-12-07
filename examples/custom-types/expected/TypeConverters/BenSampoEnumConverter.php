<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\TypeConverters;

class BenSampoEnumConverter implements \Spawnia\Sailor\Convert\TypeConverter
{
    public function fromGraphQL($value): \Spawnia\Sailor\CustomTypes\Types\BenSampoEnum
    {
        return new \Spawnia\Sailor\CustomTypes\Types\BenSampoEnum($value);
    }

    public function toGraphQL($value)
    {
        if (! $value instanceof \Spawnia\Sailor\CustomTypes\Types\BenSampoEnum) {
            throw new \InvalidArgumentException('Expected instanceof Spawnia\Sailor\CustomTypes\Types\BenSampoEnum, got: '.gettype($value));
        }

        return $value->value;
    }
}
