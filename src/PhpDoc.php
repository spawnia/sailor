<?php

namespace Spawnia\Sailor;

use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\Type;

class PhpDoc
{
    public static function forType(Type $type): string
    {
        $nullable = true;
        if($type instanceof NonNull) {
            $nullable = false;
            $type = $type->getWrappedType();
        }

        $list = false;
        if($type instanceof ListOfType) {
            $list = true;
            $type = $type->getWrappedType(true);
        }
        // TODO https://github.com/spawnia/sailor/issues/1

        $doc = $type->name;

        if($list) {
            $doc .= '[]';
        }

        if($nullable) {
            $doc .= '|null';
        }

        return $doc;
    }
}
