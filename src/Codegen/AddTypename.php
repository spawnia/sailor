<?php declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Language\AST\DocumentNode;
use GraphQL\Language\AST\FieldNode;
use GraphQL\Language\AST\InlineFragmentNode;
use GraphQL\Language\AST\OperationDefinitionNode;
use GraphQL\Language\AST\SelectionSetNode;
use GraphQL\Language\Parser;
use GraphQL\Type\Introspection;

class AddTypename
{
    public static function modify(DocumentNode &$document): void
    {
        foreach ($document->definitions as $definition) {
            if ($definition instanceof OperationDefinitionNode) {
                static::ensurePresent($definition->selectionSet);
            }
        }
    }

    protected static function ensurePresent(SelectionSetNode $selectionSetNode): void
    {
        static::purgeRedundant($selectionSetNode);

        $selectionSetNode->selections->splice(
            0,
            0,
            [Parser::field(Introspection::TYPE_NAME_FIELD_NAME)]
        );
    }

    protected static function purgeRedundant(SelectionSetNode $selectionSetNode): void
    {
        $selections = $selectionSetNode->selections;

        foreach ($selections as $i => $selection) {
            if ($selection instanceof FieldNode) {
                if ($selection->name->value === Introspection::TYPE_NAME_FIELD_NAME) {
                    unset($selections[$i]);
                }

                $subSelectionSet = $selection->selectionSet;
                if ($subSelectionSet !== null) {
                    static::ensurePresent($subSelectionSet);
                }
            } elseif ($selection instanceof InlineFragmentNode) {
                static::purgeRedundant($selection->selectionSet);
            }
        }

        // Restores numeric indexes
        // TODO use ->reindex() with graphql-php 15
        $selections->splice(0, 0, []);
    }
}
