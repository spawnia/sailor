<?php declare(strict_types=1);

namespace Spawnia\Sailor\Input\Operations\TakeList;

class TakeListErrorFreeResult extends \Spawnia\Sailor\ErrorFreeResult
{
    public TakeList $data;

    public static function endpoint(): string
    {
        return 'input';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../sailor.php');
    }
}
