<?php declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Types;

class __TypeKind
{
    public const SCALAR = 'SCALAR';
    public const OBJECT = 'OBJECT';
    public const INTERFACE = 'INTERFACE';
    public const UNION = 'UNION';
    public const ENUM = 'ENUM';
    public const INPUT_OBJECT = 'INPUT_OBJECT';
    public const LIST = 'LIST';
    public const NON_NULL = 'NON_NULL';

    public static function endpoint(): string
    {
        return 'custom-types';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../sailor.php');
    }
}
