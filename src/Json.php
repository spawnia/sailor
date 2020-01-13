<?php

declare(strict_types=1);

namespace Spawnia\Sailor;

class Json
{
    /**
     * Convert an JSON encodable value so that maps are \stdClass instances.
     *
     * @param mixed $value Any value that can be encoded as JSON.
     * @return mixed The transformed value where associative arrays are now \stcClass instances.
     */
    public static function assocToStdClass($value)
    {
        return \Safe\json_decode(
            \Safe\json_encode($value)
        );
    }

    /**
     * Convert an JSON encodable value so that maps are associative arrays.
     *
     * @param mixed $value Any value that can be encoded as JSON.
     * @return mixed The transformed value where objects are now associative arrays.
     */
    public static function stdClassToAssoc($value)
    {
        return \Safe\json_decode(
            \Safe\json_encode($value),
            true
        );
    }
}
