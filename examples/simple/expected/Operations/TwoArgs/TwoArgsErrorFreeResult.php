<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\TwoArgs;

class TwoArgsErrorFreeResult extends \Spawnia\Sailor\ErrorFreeResult
{
    public TwoArgs $data;

    public static function endpoint(): string
    {
        return 'simple';
    }

    public static function config(): string
    {
        return __DIR__ . '/../../../sailor.php';
    }
}
