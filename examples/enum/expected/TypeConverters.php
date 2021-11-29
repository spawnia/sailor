<?php

namespace Spawnia\Sailor\Enum;

class TypeConverters
{
    public static function Int(): \Spawnia\Sailor\TypeConverter
    {
        static $IntConverter;
        return $IntConverter ??= new \Spawnia\Sailor\TypeConverter\IntConverter();
    }

    public static function CustomEnum(): \Spawnia\Sailor\TypeConverter
    {
        static $CustomEnumConverter;
        return $CustomEnumConverter ??= new \Spawnia\Sailor\Enum\TypeConverters\CustomEnumConverter();
    }

    public static function DefaultEnum(): \Spawnia\Sailor\TypeConverter
    {
        static $DefaultEnumConverter;
        return $DefaultEnumConverter ??= new \Spawnia\Sailor\TypeConverter\EnumConverter();
    }
}
