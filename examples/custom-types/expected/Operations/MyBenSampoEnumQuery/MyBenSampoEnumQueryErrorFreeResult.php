<?php declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations\MyBenSampoEnumQuery;

class MyBenSampoEnumQueryErrorFreeResult extends \Spawnia\Sailor\ErrorFreeResult
{
    public MyBenSampoEnumQuery $data;

    public static function endpoint(): string
    {
        return 'custom-types';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../sailor.php');
    }
}
