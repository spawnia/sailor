<?php declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Language\AST\DocumentNode;
use GraphQL\Language\AST\FragmentDefinitionNode;
use GraphQL\Language\AST\NodeList;
use GraphQL\Language\AST\OperationDefinitionNode;

class Merger
{
    /**
     * @param  array<string, DocumentNode>  $documents
     */
    public static function combine(array $documents): DocumentNode
    {
        $definitions = new NodeList([]);

        /** @var DocumentNode $document */
        foreach ($documents as $document) {
            /** @var OperationDefinitionNode|FragmentDefinitionNode $definition */
            foreach ($document->definitions as $definition) {
                /** @var string $name We validated that operations are always named */
                $name = $definition->name->value;

                $definitions[$name] = $definition;
            }
        }

        return new DocumentNode([
            'definitions' => $definitions,
        ]);
    }
}
