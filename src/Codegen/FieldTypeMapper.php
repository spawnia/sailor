<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

final class FieldTypeMapper
{
    public const SUFFIX = 'TypeMapper';

    public static function methodName(string $field): string
    {
        return $field . self::SUFFIX;
    }

    public static function fieldName(string $mapTypeMethod): string
    {
        return \Safe\substr(
            $mapTypeMethod,
            0,
            -strlen(self::SUFFIX)
        );
    }
}
