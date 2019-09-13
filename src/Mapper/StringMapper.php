<?php

namespace Spawnia\Sailor\Mapper;

use Spawnia\Sailor\TypeMapper;

class StringMapper implements TypeMapper
{
    /**
     * Strings are not converted.
     *
     * @param  string  $value
     * @return string
     */
    public function __invoke($value): string
    {
        return $value;
    }
}
