<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Language\AST\DocumentNode;
use GraphQL\Language\AST\NodeList;

class Merger
{
    /**
     * @param  array<string, DocumentNode>  $documents
     */
    public static function combine(array $documents): DocumentNode
    {
        /** @var DocumentNode $root */
        $root = array_pop($documents);

        // @phpstan-ignore-next-line Contravariance
        $root->definitions = array_reduce(
            $documents,
            static function (NodeList $definitions, DocumentNode $document): NodeList {
                // @phpstan-ignore-next-line Contravariance
                return $definitions->merge($document->definitions);
            },
            $root->definitions
        );

        return $root;
    }
}
