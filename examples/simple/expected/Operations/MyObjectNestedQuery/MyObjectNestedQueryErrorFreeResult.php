<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\MyObjectNestedQuery;

class MyObjectNestedQueryErrorFreeResult extends \Spawnia\Sailor\ErrorFreeResult
{
    public MyObjectNestedQuery $data;

    public static function config(): string
    {
        return '/home/bfranke/projects/sailor/examples/simple/sailor.php';
    }

    public static function endpoint(): string
    {
        return 'simple';
    }
}
