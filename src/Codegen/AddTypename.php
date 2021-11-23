<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Language\AST\DocumentNode;
use GraphQL\Language\AST\FieldNode;
use GraphQL\Language\AST\InlineFragmentNode;
use GraphQL\Language\AST\OperationDefinitionNode;
use GraphQL\Language\AST\SelectionNode;
use GraphQL\Language\AST\SelectionSetNode;
use GraphQL\Language\Parser;
use GraphQL\Type\Introspection;

class AddTypename
{
    public static function modify(DocumentNode &$document): void
    {
        foreach ($document->definitions as $definition) {
            if ($definition instanceof OperationDefinitionNode) {
                static::ensure($definition->selectionSet);
            }
        }
    }

    protected static function ensure(SelectionSetNode $selectionSetNode): void
    {
        $hasTypename = false;
        foreach ($selectionSetNode->selections as $selection) {
            if (static::isTypenameField($selection)) {
                $hasTypename = true;
            }

            if ($selection instanceof InlineFragmentNode) {
                static::purge($selection->selectionSet);
            }

            if ($selection instanceof FieldNode) {
                $subSelectionSet = $selection->selectionSet;
                if ($subSelectionSet !== null) {
                    static::ensure($subSelectionSet);
                }
            }
        }

        if (! $hasTypename) {
            $selectionSetNode->selections[] = Parser::field(Introspection::TYPE_NAME_FIELD_NAME);
        }
    }

    protected static function purge(SelectionSetNode $selectionSet): void
    {
        $selections = $selectionSet->selections;
        foreach ($selections as $i => $selection) {
            if (self::isTypenameField($selection)) {
                unset($selections[$i]);
            }

            if ($selection instanceof FieldNode) {
                $subSelectionSet = $selection->selectionSet;
                if ($subSelectionSet !== null) {
                    static::ensure($subSelectionSet);
                }
            }
        }
    }

    protected static function isTypenameField(SelectionNode $selection): bool
    {
        return $selection instanceof FieldNode
            && $selection->name->value === Introspection::TYPE_NAME_FIELD_NAME;
    }
}
