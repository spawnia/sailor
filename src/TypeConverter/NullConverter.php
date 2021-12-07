<?php declare(strict_types=1);

namespace Spawnia\Sailor\TypeConverter;

use Spawnia\Sailor\TypeConverter;

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
        return null === $value
            ? null
            : $this->ofType->fromGraphQL($value);
    }

    public function toGraphQL($value)
    {
        return null === $value
            ? null
            : $this->ofType->toGraphQL($value);
    }
}
