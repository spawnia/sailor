<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Types;

class CustomEnum extends \Spawnia\Sailor\CustomTypesSrc\Enum
{
    public const A = 'A';
    public const B = 'B';

    public static function endpoint(): string
    {
        return 'custom-types';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../sailor.php');
    }
}
