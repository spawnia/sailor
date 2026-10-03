<?php declare(strict_types=1);

namespace Spawnia\Sailor\Convert;

/** Marks a field the server may omit due to @skip or @include. */
class OmittableConverter implements TypeConverter
{
    protected TypeConverter $ofType;

    public function __construct(TypeConverter $ofType)
    {
        $this->ofType = $ofType;
    }

    public function fromGraphQL($value)
    {
        return $this->ofType->fromGraphQL($value);
    }

    public function toGraphQL($value)
    {
        return $this->ofType->toGraphQL($value);
    }
}
