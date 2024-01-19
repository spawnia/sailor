<?php declare(strict_types=1);

namespace Spawnia\Sailor\Convert;

/**
 * Short-circuit conversion of null.
 */
class NullConverter implements TypeConverter
{
    protected TypeConverter $ofType;

    public function __construct(TypeConverter $ofType)
    {
        $this->ofType = $ofType;
    }

    public function fromGraphQL($value)
    {
        return $value === null
            ? null
            : $this->ofType->fromGraphQL($value);
    }

    public function toGraphQL($value)
    {
        return $value === null
            ? null
            : $this->ofType->toGraphQL($value);
    }
}
