<?php declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\Type;
use Spawnia\Sailor\Convert\ListConverter;
use Spawnia\Sailor\Convert\NonNullConverter;
use Spawnia\Sailor\Convert\NullConverter;

class TypeWrapper
{
    public static function phpDoc(Type $type, string $typeReference, bool $shouldWrapWithNull = true): string
    {
        if ($type instanceof NonNull) {
            return self::phpDoc($type->getWrappedType(), $typeReference, false);
        }

        if ($shouldWrapWithNull) {
            $nullable = self::phpDoc($type, $typeReference, false);

            return "{$nullable}|null";
        }

        if ($type instanceof ListOfType) {
            $inArray = self::phpDoc($type->getWrappedType(), $typeReference);

            return "array<int, {$inArray}>";
        }

        return $typeReference;
    }

    /**
     * Wrap the code for an inner converter with converters for its wrapping types.
     */
    public static function converter(Type $type, string $innerConverter, bool $shouldWrapWithNull = true): string
    {
        if ($type instanceof NonNull) {
            $nonNullable = self::converter($type->getOfType(), $innerConverter, false);

            $nonNullConverterClass = NonNullConverter::class;

            return /** @lang PHP */ "new \\{$nonNullConverterClass}({$nonNullable})";
        }

        if ($shouldWrapWithNull) {
            $nullable = self::converter($type, $innerConverter, false);

            $nullConverterClass = NullConverter::class;

            return /** @lang PHP */ "new \\{$nullConverterClass}({$nullable})";
        }

        if ($type instanceof ListOfType) {
            $inList = self::converter($type->getOfType(), $innerConverter);

            $listConverterClass = ListConverter::class;

            return /** @lang PHP */ "new \\{$listConverterClass}({$inList})";
        }

        return $innerConverter;
    }

    public static function php(Type $type, string $typeReference): string
    {
        if ($type instanceof NonNull) {
            return self::php($type->getWrappedType(), $typeReference);
        }

        if ($type instanceof ListOfType) {
            return 'array';
        }

        return $typeReference;
    }
}
