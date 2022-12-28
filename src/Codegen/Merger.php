<?php declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Language\AST\DocumentNode;
use GraphQL\Language\AST\NodeList;

class Merger
{
    /**
     * @param array<string, DocumentNode> $documents
     */
    public static function combine(array $documents): DocumentNode
    {
        $root = array_pop($documents);
        assert($root instanceof DocumentNode);

        // @phpstan-ignore-next-line Contravariance
        $root->definitions = array_reduce(
            $documents,
            // @phpstan-ignore-next-line Contravariance
            static fn(NodeList $definitions, DocumentNode $document): NodeList => $definitions->merge($document->definitions),
            $root->definitions
        );

        return $root;
    }
}
