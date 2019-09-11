<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Type\Definition\BooleanType;
use GraphQL\Type\Definition\IDType;
use GraphQL\Type\Definition\IntType;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\ListOfType;
use PHPStan\Type\FloatType;

class PhpDoc
{
    public static function forType(Type $type, string $typeReference): string
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
        switch(get_class($type)) {
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
}
