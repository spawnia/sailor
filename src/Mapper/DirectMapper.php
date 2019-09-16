<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Mapper;

use Spawnia\Sailor\TypeMapper;

class DirectMapper implements TypeMapper
{
    /**
     * Primitive scalar types do not require conversion.
     *
     * @param  string|int|float|bool  $value
     * @return string|int|float|bool
     */
    public function __invoke($value)
    {
        return $value;
    }
}
