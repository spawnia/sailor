<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Types;

enum NativeEnum: string
{
    case A = 'A';
    case B = 'B';

    public static function endpoint(): string
    {
        return 'custom-types';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../sailor.php');
    }
}
