<?php declare(strict_types=1);

namespace Spawnia\Sailor\Convert;

/** Does no conversion, because custom scalars are opaque without further knowledge and bespoke implementations. */
class ScalarConverter implements TypeConverter
{
    public function fromGraphQL($value)
    {
        return $value;
    }

    public function toGraphQL($value)
    {
        return $value;
    }
}
