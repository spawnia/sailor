<?php declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\NonNull;
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

            return "array<int, {$inArray}>";
        }

        return $typeReference;
    }
}
