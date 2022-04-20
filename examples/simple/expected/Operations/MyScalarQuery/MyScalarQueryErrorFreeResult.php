<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\MyScalarQuery;

class MyScalarQueryErrorFreeResult extends \Spawnia\Sailor\ErrorFreeResult
{
    public MyScalarQuery $data;

    public static function config(): string
    {
        return '/home/bfranke/projects/sailor/tests/Integration/../../examples/simple/sailor.php';
    }

    public static function endpoint(): string
    {
        return 'simple';
    }
}
