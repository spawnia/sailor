<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Input\Operations\TakeSomeInput;

class TakeSomeInputErrorFreeResult extends \Spawnia\Sailor\ErrorFreeResult
{
    public TakeSomeInput $data;

    public static function endpoint(): string
    {
        return 'input';
    }

    public static function config(): string
    {
        return __DIR__ . '/../../../sailor.php';
    }
}
