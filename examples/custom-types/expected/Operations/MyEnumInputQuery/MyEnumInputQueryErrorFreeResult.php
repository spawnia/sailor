<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations\MyEnumInputQuery;

class MyEnumInputQueryErrorFreeResult extends \Spawnia\Sailor\ErrorFreeResult
{
    public MyEnumInputQuery $data;

    public static function endpoint(): string
    {
        return 'custom-types';
    }

    public static function config(): string
    {
        return __DIR__ . '/../../../sailor.php';
    }
}
