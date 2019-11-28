<?php

namespace Spawnia\Sailor;

class Json
{
    /**
     * @param mixed $value
     * @return mixed
     */
    public static function assocToStdClass($value)
    {
        return \Safe\json_decode(
            \Safe\json_encode($value)
        );
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public static function stdClassToAssoc($value)
    {
        return \Safe\json_decode(
            \Safe\json_encode($value),
            true
        );
    }
}
