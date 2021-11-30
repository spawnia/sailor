<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple;

class TypeConverters
{
    public function Int(): \Spawnia\Sailor\TypeConverter\IntConverter
    {
        static $converter;
        return $converter ??= new \Spawnia\Sailor\TypeConverter\IntConverter();
    }

    public function Float(): \Spawnia\Sailor\TypeConverter\FloatConverter
    {
        static $converter;
        return $converter ??= new \Spawnia\Sailor\TypeConverter\FloatConverter();
    }

    public function String(): \Spawnia\Sailor\TypeConverter\StringConverter
    {
        static $converter;
        return $converter ??= new \Spawnia\Sailor\TypeConverter\StringConverter();
    }

    public function Boolean(): \Spawnia\Sailor\TypeConverter\BooleanConverter
    {
        static $converter;
        return $converter ??= new \Spawnia\Sailor\TypeConverter\BooleanConverter();
    }

    public function ID(): \Spawnia\Sailor\TypeConverter\IDConverter
    {
        static $converter;
        return $converter ??= new \Spawnia\Sailor\TypeConverter\IDConverter();
    }

    public function SomeInput(): Inputs\SomeInput
    {
        static $converter;
        return $converter ??= new \Spawnia\Sailor\Simple\Inputs\SomeInput();
    }

    public function SomeEnum(): \Spawnia\Sailor\TypeConverter\EnumConverter
    {
        static $converter;
        return $converter ??= new \Spawnia\Sailor\TypeConverter\EnumConverter();
    }

    public function __TypeKind(): \Spawnia\Sailor\TypeConverter\EnumConverter
    {
        static $converter;
        return $converter ??= new \Spawnia\Sailor\TypeConverter\EnumConverter();
    }

    public function __DirectiveLocation(): \Spawnia\Sailor\TypeConverter\EnumConverter
    {
        static $converter;
        return $converter ??= new \Spawnia\Sailor\TypeConverter\EnumConverter();
    }
}
