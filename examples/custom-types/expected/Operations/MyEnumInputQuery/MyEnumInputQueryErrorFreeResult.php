<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations\MyEnumInputQuery;

class MyEnumInputQueryErrorFreeResult extends \Spawnia\Sailor\ErrorFreeResult
{
    public MyEnumInputQuery $data;

    public static function config(): string
    {
        return '/home/bfranke/projects/sailor/tests/Integration/../../examples/custom-types/sailor.php';
    }

    public static function endpoint(): string
    {
        return 'custom-types';
    }
}
