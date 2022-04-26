<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Types;

class DefaultEnum
{
    public const A = 'A';
    public const B = 'B';

    public static function endpoint(): string
    {
        return 'custom-types';
    }

    public static function config(): string
    {
        return __DIR__ . '/../../sailor.php';
    }
}
