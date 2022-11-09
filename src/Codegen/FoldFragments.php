<?php declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Language\AST\DocumentNode;
use GraphQL\Language\AST\FieldNode;
use GraphQL\Language\AST\FragmentDefinitionNode;
use GraphQL\Language\AST\FragmentSpreadNode;
use GraphQL\Language\AST\InlineFragmentNode;
use GraphQL\Language\AST\NameNode;
use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\NodeList;
use GraphQL\Language\AST\OperationDefinitionNode;
use GraphQL\Language\AST\SelectionNode;
use GraphQL\Language\AST\SelectionSetNode;

class FoldFragments
{
    protected DocumentNode $document;

    /**
     * @var array<string, OperationDefinitionNode>
     */
    protected array $operations = [];

    /**
     * @var array<string, FragmentDefinitionNode>
     */
    protected array $fragments = [];

    public function __construct(DocumentNode $document)
    {
        $this->document = $document;
    }

    public function modify(): DocumentNode
    {
        foreach ($this->document->definitions as $definition) {
            if ($definition instanceof OperationDefinitionNode) {
                $name = $definition->name;
                assert($name instanceof NameNode, 'Validated at this point');

                $this->operations[$name->value] = $definition;
            }

            if ($definition instanceof FragmentDefinitionNode) {
                $this->fragments[$definition->name->value] = $definition;
            }
        }

        foreach ($this->fragments as $definition) {
            $this->modifySelectionSet($definition->selectionSet);
        }

        foreach ($this->operations as $definition) {
            $this->modifySelectionSet($definition->selectionSet);
        }

        return new DocumentNode([
            'definitions' => new NodeList(array_values($this->operations)),
        ]);
    }

    /**
     * @return NodeList<Node&SelectionNode>
     */
    protected function extractFields(SelectionSetNode $selectionSet): NodeList
    {
        /** @var array<int, Node&SelectionNode> $selections */
        $selections = [];

        foreach ($selectionSet->selections as $selection) {
            if ($selection instanceof FieldNode) {
                $subSelection = $selection->selectionSet;
                if ($subSelection instanceof SelectionSetNode) {
                    $this->modifySelectionSet($subSelection);
                }

                $selections[] = $selection;
            }

            if ($selection instanceof FragmentSpreadNode) {
                $selectionName = $selection->name->value;
                $fragment = $this->fragments[$selectionName] ?? null;
                if (! $fragment instanceof FragmentDefinitionNode) {
                    throw new \Exception("Found fragment spread referencing undefined fragment {$selectionName}.");
                }

                $fragmentSelectionSet = $fragment->selectionSet;
                $this->modifySelectionSet($fragmentSelectionSet);

                if (count($fragment->directives) > 0) {
                    throw new \Exception("Found directives on fragment {$fragment->name->value}, but can not use it because they will be inlined.");
                }

                // @phpstan-ignore-next-line TODO remove with graphql-php 15
                $selections[] = new InlineFragmentNode([
                    'typeCondition' => $fragment->typeCondition,
                    'directives' => $selection->directives,
                    'selectionSet' => $fragmentSelectionSet,
                ]);
            }

            if ($selection instanceof InlineFragmentNode) {
                $this->modifySelectionSet($selection->selectionSet);
                $selections[] = $selection;
            }
        }

        return new NodeList($selections);
    }

    protected function modifySelectionSet(SelectionSetNode $selectionSet): void
    {
        $selectionSet->selections = $this->extractFields($selectionSet);
    }
}
