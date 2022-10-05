<?php

declare(strict_types=1);

namespace Spawnia\Sailor\PhpKeywords\Types;

class _abstract
{
    public const _class = 'class';

    public static function endpoint(): string
    {
        return 'php-keywords';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../sailor.php');
    }
}
