<?php

declare(strict_types=1);

namespace Spawnia\Sailor;

class Json
{
    public static function assocToStdClass($value)
    {
        return \Safe\json_decode(
            \Safe\json_encode($value)
        );
    }

    public static function stdClassToAssoc($value)
    {
        return \Safe\json_decode(
            \Safe\json_encode($value),
            true
        );
    }
}
