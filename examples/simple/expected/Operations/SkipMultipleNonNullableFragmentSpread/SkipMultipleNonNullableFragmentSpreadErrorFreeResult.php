<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\SkipMultipleNonNullableFragmentSpread;

class SkipMultipleNonNullableFragmentSpreadErrorFreeResult extends \Spawnia\Sailor\ErrorFreeResult
{
    public SkipMultipleNonNullableFragmentSpread $data;

    public static function endpoint(): string
    {
        return 'simple';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../sailor.php');
    }
}
