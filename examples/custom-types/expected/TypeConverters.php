<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes;

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

    public static function EnumInput(): Types\EnumInput
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\CustomTypes\Types\EnumInput();
    }

    public static function DefaultEnum(): \Spawnia\Sailor\Convert\EnumConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\Convert\EnumConverter();
    }

    public static function CustomEnum(): TypeConverters\CustomEnumConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\CustomTypes\TypeConverters\CustomEnumConverter();
    }

    public static function BenSampoEnum(): TypeConverters\BenSampoEnumConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\CustomTypes\TypeConverters\BenSampoEnumConverter();
    }

    public static function DefaultDate(): \Spawnia\Sailor\Convert\ScalarConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\Convert\ScalarConverter();
    }

    public static function CustomDate(): TypeConverters\CustomDateConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\CustomTypes\TypeConverters\CustomDateConverter();
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

    public static function endpoint(): string
    {
        return 'custom-types';
    }

    public static function config(): string
    {
        return __DIR__ . '/../sailor.php';
    }
}
