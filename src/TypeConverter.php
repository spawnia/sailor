<?php declare(strict_types=1);

namespace Spawnia\Sailor;

/**
 * Converts a value between its JSON representation and an internal value.
 */
interface TypeConverter
{
    /**
     * Convert from a JSON value to an internal value.
     *
     * @param  array<int, mixed>|\stdClass|string|float|int|bool|null  $value  the value given by the server
     *
     * @return mixed the internal representation of the given value
     */
    public function fromGraphQL($value);

    /**
     * Convert from an internal value to a JSON value.
     *
     * @param  mixed  $value  the internal representation of the given value
     *
     * @return array<int, mixed>|\stdClass|string|float|int|bool|null the value to pass to the server
     */
    public function toGraphQL($value);
}
