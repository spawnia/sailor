<?php declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\TypeConverters;

class NativeEnumConverter extends \Spawnia\Sailor\Convert\NativeEnumConverter
{
    protected static function enumClass(): string
    {
        return \Spawnia\Sailor\CustomTypes\Types\NativeEnum::class;
    }

    public static function endpoint(): string
    {
        return 'custom-types';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../sailor.php');
    }
}
