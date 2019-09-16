<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use PHPStan\Type\FloatType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\IntType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Type\Definition\BooleanType;

class PhpType
{
    public static function phpDoc(Type $type, string $typeReference): string
    {
        [
            'nullable' => $nullable,
            'list' => $list
        ] = self::wrappedTypeInfo($type);

        // TODO https://github.com/spawnia/sailor/issues/1

        if ($list) {
            $typeReference .= '[]';
        }

        if ($nullable) {
            $typeReference .= '|null';
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

    public static function wrappedTypeInfo(Type $type): array
    {
        $nullable = true;
        if ($type instanceof NonNull) {
            $nullable = false;
            $type = $type->getWrappedType();
        }

        $list = false;
        if ($type instanceof ListOfType) {
            $list = true;
        }

        return [
            'nullable' => $nullable,
            'list' => $list,
        ];
    }
}
