<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\_catch;

class catchErrorFreeResult extends \Spawnia\Sailor\ErrorFreeResult
{
    public catch $data;

    public static function endpoint(): string
    {
        return 'php-keywords';
    }

    public static function config(): string
    {
        return __DIR__ . '/../../../sailor.php';
    }
}
