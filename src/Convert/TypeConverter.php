<?php declare(strict_types=1);

namespace Spawnia\Sailor\Convert;

use Spawnia\Sailor\Json;

/**
 * Converts a value between its JSON representation and an internal value.
 *
 * @phpstan-import-type StdClassJsonValue from Json
 */
interface TypeConverter
{
    /**
     * Convert from a JSON value to an internal value.
     *
     * @param StdClassJsonValue $value the value given by the server
     *
     * @return mixed the internal representation of the given value
     */
    public function fromGraphQL($value);

    /**
     * Convert from an internal value to a JSON value.
     *
     * @param mixed $value the internal representation of the given value
     *
     * @return StdClassJsonValue the value to pass to the server
     */
    public function toGraphQL($value);
}
