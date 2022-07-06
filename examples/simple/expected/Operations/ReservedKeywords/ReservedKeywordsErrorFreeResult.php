<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\ReservedKeywords;

class ReservedKeywordsErrorFreeResult extends \Spawnia\Sailor\ErrorFreeResult
{
    public ReservedKeywords $data;

    public static function endpoint(): string
    {
        return 'simple';
    }

    public static function config(): string
    {
        return __DIR__ . '/../../../sailor.php';
    }
}
