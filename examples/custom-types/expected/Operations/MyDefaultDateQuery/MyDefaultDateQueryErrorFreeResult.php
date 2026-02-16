<?php declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations\MyDefaultDateQuery;

class MyDefaultDateQueryErrorFreeResult extends \Spawnia\Sailor\ErrorFreeResult
{
    public MyDefaultDateQuery $data;

    public static function endpoint(): string
    {
        return 'custom-types';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../sailor.php');
    }
}
