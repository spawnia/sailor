<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Language\AST\NodeList;
use GraphQL\Language\AST\DocumentNode;

class Merger
{
    public static function combine(array $documents): DocumentNode
    {
        /** @var DocumentNode $root */
        $root = array_pop($documents);

        $root->definitions = array_reduce(
            $documents,
            function (NodeList $definitions, DocumentNode $document): NodeList {
                return $definitions->merge($document->definitions);
            },
            $root->definitions
        );

        return $root;
    }
}
