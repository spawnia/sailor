<?php declare(strict_types=1);

namespace Spawnia\Sailor\Convert;

abstract class NativeEnumConverter implements TypeConverter
{
    public function fromGraphQL($value): \UnitEnum
    {
        return static::enumClass()::from($value);
    }

    public function toGraphQL($value): string
    {
        if (! $value instanceof \BackedEnum) {
            $notEnum = gettype($value);
            throw new \InvalidArgumentException("Expected \BackedEnum, got {$notEnum}.");
        }

        $actualEnumClass = get_class($value);
        $expectedEnumClass = static::enumClass();
        if ($actualEnumClass !== $expectedEnumClass) {
            $notEnum = get_class($value);
            throw new \InvalidArgumentException("Expected instanceof {$expectedEnumClass}, got {$notEnum}.");
        }

        return $value->name;
    }

    /** @return class-string<\BackedEnum> */
    abstract protected static function enumClass(): string;
}
