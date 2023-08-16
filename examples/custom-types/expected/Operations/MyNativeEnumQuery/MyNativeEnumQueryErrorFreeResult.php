<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations\MyNativeEnumQuery;

class MyNativeEnumQueryErrorFreeResult extends \Spawnia\Sailor\ErrorFreeResult
{
    public MyNativeEnumQuery $data;

    public static function endpoint(): string
    {
        return 'custom-types';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../sailor.php');
    }
}
