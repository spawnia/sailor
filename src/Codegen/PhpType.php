<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Type\Definition\BooleanType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\FloatType;
use GraphQL\Type\Definition\IntType;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Type\Definition\Type;

class PhpType
{
    public static function phpDoc(Type $type, string $typeReference, bool $shouldWrapWithNull = true): string
    {
        if ($type instanceof NonNull) {
            return self::phpDoc(
                $type->getWrappedType(),
                $typeReference,
                false
            );
        }

        if ($shouldWrapWithNull) {
            $nullable = self::phpDoc($type, $typeReference, false);

            return "{$nullable}|null";
        }

        if ($type instanceof ListOfType) {
            $inArray = self::phpDoc($type->getWrappedType(), $typeReference);

            return "array<{$inArray}>";
        }

        return $typeReference;
    }

    public static function forScalar(ScalarType $type): string
    {
        switch (get_class($type)) {
            case IntType::class:
                return 'int';
            case FloatType::class:
                return 'float';
            case BooleanType::class:
                return 'bool';
            default:
                // Includes ID, String and all other scalars
                return 'string';
        }
    }

    public static function forEnum(EnumType $type): string
    {
        // TODO add a comment that lists the instances
        // or do something even more fancy
        return 'string';
    }
}
