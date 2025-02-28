<?php declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations\MyCustomEnumQuery;

class MyCustomEnumQueryErrorFreeResult extends \Spawnia\Sailor\ErrorFreeResult
{
    public MyCustomEnumQuery $data;

    public static function endpoint(): string
    {
        return 'custom-types';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../sailor.php');
    }
}
