<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\TypeConverters;

class CustomDateConverter implements \Spawnia\Sailor\TypeConverter
{
    public function fromGraphQL($value): \DateTime
    {
        return \DateTime::createFromFormat('Y-m-d H:i:s', $value);
    }

    public function toGraphQL($value)
    {
        if (! $value instanceof \DateTime) {
            throw new \InvalidArgumentException('Expected instanceof DateTime, got: '.gettype($value));
        }

        return $value->format('Y-m-d H:i:s');
    }
}
