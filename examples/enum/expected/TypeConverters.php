<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Enum;

class TypeConverters
{
    public static function Int(): \Spawnia\Sailor\TypeConverter\IntConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\TypeConverter\IntConverter();
    }

    public static function Float(): \Spawnia\Sailor\TypeConverter\FloatConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\TypeConverter\FloatConverter();
    }

    public static function String(): \Spawnia\Sailor\TypeConverter\StringConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\TypeConverter\StringConverter();
    }

    public static function Boolean(): \Spawnia\Sailor\TypeConverter\BooleanConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\TypeConverter\BooleanConverter();
    }

    public static function ID(): \Spawnia\Sailor\TypeConverter\IDConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\TypeConverter\IDConverter();
    }

    public static function DefaultEnum(): \Spawnia\Sailor\TypeConverter\EnumConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\TypeConverter\EnumConverter();
    }

    public static function CustomEnum(): TypeConverters\CustomEnum
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\Enum\TypeConverters\CustomEnum();
    }

    public static function EnumInput(): Inputs\EnumInput
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\Enum\Inputs\EnumInput();
    }

    public static function __TypeKind(): \Spawnia\Sailor\TypeConverter\EnumConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\TypeConverter\EnumConverter();
    }

    public static function __DirectiveLocation(): \Spawnia\Sailor\TypeConverter\EnumConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\TypeConverter\EnumConverter();
    }
}