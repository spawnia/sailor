<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Mapper;

use Spawnia\Sailor\TypeMapper;

class DirectMapper implements TypeMapper
{
    /**
     * Primitive scalar types do not require conversion.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function __invoke($value)
    {
        return $value;
    }
}
