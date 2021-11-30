<?php

namespace Spawnia\Sailor\Codegen;

use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\Type;
use Spawnia\Sailor\TypeConverter\ListConverter;
use Spawnia\Sailor\TypeConverter\NonNullConverter;
use Spawnia\Sailor\TypeConverter\NullConverter;

class TypeConverterWrapper
{
    /**
     * Wrap the code for an inner converter with converters for its wrapping types.
     */
    public static function wrap(Type $type, string $innerConverter): string
    {
        if ($type instanceof NonNull) {
            $nonNullConverterClass = NonNullConverter::class;

            return self::wrap(
                $type->getOfType(),
                /** @lang PHP */ "new \\{$nonNullConverterClass}({$innerConverter})"
            );
        }

        if ($type instanceof ListOfType) {
            $listConverterClass = ListConverter::class;

            return self::wrap(
                $type->getOfType(),
                /** @lang PHP */ "new \\{$listConverterClass}({$innerConverter})"
            );
        }

        $nullConverterClass = NullConverter::class;

        return /** @lang PHP */ "new \\{$nullConverterClass}({$innerConverter})";
    }
}
