<?php

declare(strict_types=1);

namespace Spawnia\Sailor\PhpKeywords\Operations\_catch;

class _catchErrorFreeResult extends \Spawnia\Sailor\ErrorFreeResult
{
    public _catch $data;

    public static function endpoint(): string
    {
        return 'php-keywords';
    }

    public static function config(): string
    {
        return __DIR__ . '/../../../sailor.php';
    }
}
