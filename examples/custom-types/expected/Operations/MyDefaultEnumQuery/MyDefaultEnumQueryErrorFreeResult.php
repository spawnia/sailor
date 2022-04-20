<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations\MyDefaultEnumQuery;

class MyDefaultEnumQueryErrorFreeResult extends \Spawnia\Sailor\ErrorFreeResult
{
    public MyDefaultEnumQuery $data;

    public static function config(): string
    {
        return '/home/bfranke/projects/sailor/tests/Integration/../../examples/custom-types/sailor.php';
    }

    public static function endpoint(): string
    {
        return 'custom-types';
    }
}
