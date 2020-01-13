<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Mapper;

use Spawnia\Sailor\TypeMapper;

class DirectMapper implements TypeMapper
{
    /**
     * Primitive scalar types do not require conversion.
     *
     * @param mixed $value Can be an arbitrary PHP primitive.
     * @return mixed The unchanged $value.
     */
    public function __invoke($value)
    {
        return $value;
    }
}
