<?php

declare(strict_types=1);

namespace Spawnia\Sailor;

interface TypeMapper
{
    /**
     * Map a value from JSON into a PHP type.
     *
     * @param  \stdClass|string|float|int|bool|null  $value  the value given by the server
     *
     * @return mixed the internal representation of the given value
     */
    public function __invoke($value);
}
