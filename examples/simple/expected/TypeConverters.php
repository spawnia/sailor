<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple;

class TypeConverters
{
    public static function Int(): \Spawnia\Sailor\Convert\IntConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\Convert\IntConverter();
    }

    public static function Float(): \Spawnia\Sailor\Convert\FloatConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\Convert\FloatConverter();
    }

    public static function String(): \Spawnia\Sailor\Convert\StringConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\Convert\StringConverter();
    }

    public static function Boolean(): \Spawnia\Sailor\Convert\BooleanConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\Convert\BooleanConverter();
    }

    public static function ID(): \Spawnia\Sailor\Convert\IDConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\Convert\IDConverter();
    }

    public static function SomeInput(): Types\SomeInput
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\Simple\Types\SomeInput();
    }

    public static function SomeEnum(): \Spawnia\Sailor\Convert\EnumConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\Convert\EnumConverter();
    }

    public static function __TypeKind(): \Spawnia\Sailor\Convert\EnumConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\Convert\EnumConverter();
    }

    public static function __DirectiveLocation(): \Spawnia\Sailor\Convert\EnumConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\Convert\EnumConverter();
    }
}
