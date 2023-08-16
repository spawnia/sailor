<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Types;

enum NativeEnum
{
    case A;
    case B;

    public static function endpoint(): string
    {
        return 'custom-types';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../sailor.php');
    }
}
