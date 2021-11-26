<?php

namespace Spawnia\Sailor;

/**
 * Converts a value between its JSON representation and an internal value.
 */
interface TypeConverter
{
    /**
     * Convert from a JSON value to an internal value.
     */
    public function fromGraphQL($value);

    /**
     * Convert from an internal value to a JSON value.
     */
    public function toGraphQL($value);
}
