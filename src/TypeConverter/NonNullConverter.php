<?php

declare(strict_types=1);

namespace Spawnia\Sailor\TypeConverter;

use Spawnia\Sailor\TypeConverter;

class NonNullConverter implements TypeConverter
{
    protected TypeConverter $ofType;

    public function __construct(TypeConverter $ofType)
    {
        $this->ofType = $ofType;
    }

    public function fromGraphQL($value)
    {
        if (null === $value) {
            throw new \InvalidArgumentException('Expected non-null value, got null');
        }

        return $this->ofType->fromGraphQL($value);
    }

    public function toGraphQL($value)
    {
        if (null === $value) {
            throw new \InvalidArgumentException('Expected non-null value, got null');
        }

        return $this->ofType->toGraphQL($value);
    }
}
