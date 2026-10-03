<?php declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Error\DebugFlag;
use GraphQL\Error\Error;
use GraphQL\Error\FormattedError;
use GraphQL\Language\AST\DocumentNode;
use GraphQL\Language\AST\FieldNode;
use GraphQL\Language\AST\FragmentDefinitionNode;
use GraphQL\Language\AST\FragmentSpreadNode;
use GraphQL\Language\AST\InlineFragmentNode;
use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\OperationDefinitionNode;
use GraphQL\Language\Visitor;
use GraphQL\Type\Schema;
use GraphQL\Validator\DocumentValidator;

class Validator
{
    /** @param  array<string, \GraphQL\Language\AST\DocumentNode>  $parsed */
    public static function validateDocuments(array $parsed): void
    {
        foreach ($parsed as $path => $documentNode) {
            foreach ($documentNode->definitions as $definition) {
                if ($definition instanceof OperationDefinitionNode) {
                    $nameNode = $definition->name;
                    if ($nameNode === null) {
                        throw new Error("Found unnamed operation definition in {$path}.", $definition);
                    }

                    $name = $nameNode->value;
                    $firstChar = $name[0];
                    if (strtoupper($firstChar) !== $firstChar) {
                        throw new Error("Operation names must be PascalCase, found {$name} in {$path}.", $definition);
                    }
                }

                self::rejectClientDirectives($definition, $path);
            }
        }
    }

    protected static function rejectClientDirectives(Node $definition, string $path): void
    {
        if ($definition instanceof OperationDefinitionNode) {
            $owner = "operation {$definition->name->value}"; // @phpstan-ignore-line validated to be named
        } elseif ($definition instanceof FragmentDefinitionNode) {
            $owner = "fragment {$definition->name->value}";
        } else {
            return;
        }

        Visitor::visit($definition, [
            'enter' => static function (Node $node) use ($owner, $path): void {
                if ($node instanceof FieldNode) {
                    $location = "field {$node->name->value}";
                } elseif ($node instanceof InlineFragmentNode) {
                    $typeCondition = $node->typeCondition;
                    $location = 'inline fragment' . ($typeCondition === null ? '' : " on {$typeCondition->name->value}");
                } elseif ($node instanceof FragmentSpreadNode) {
                    $location = "fragment spread ...{$node->name->value}";
                } else {
                    return;
                }

                foreach ($node->directives as $directive) {
                    $directiveName = $directive->name->value;
                    if ($directiveName === 'skip' || $directiveName === 'include') {
                        throw new Error("Sailor does not support client directives, found @{$directiveName} on {$location} in {$owner} in {$path}.", $node);
                    }
                }
            },
        ]);
    }

    public static function validateDocumentWithSchema(Schema $schema, DocumentNode $document): void
    {
        try {
            $errors = DocumentValidator::validate($schema, $document);
        } catch (\Throwable $e) {
            throw new \Exception('Unexpected error while validating a query against the schema. Check if your schema is up to date.', 0, $e);
        }

        if (count($errors) === 0) {
            return;
        }

        $formattedErrors = array_map(
            static fn (Error $error): array => FormattedError::createFromException($error, DebugFlag::INCLUDE_DEBUG_MESSAGE),
            $errors
        );
        $errorStrings = array_map(
            static fn (array $error): string => \Safe\json_encode($error),
            $formattedErrors
        );
        $errorMessage = implode("\n\n", $errorStrings);

        throw new \Exception($errorMessage);
    }
}
