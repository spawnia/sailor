<?php declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Error\DebugFlag;
use GraphQL\Error\Error;
use GraphQL\Error\FormattedError;
use GraphQL\Language\AST\DocumentNode;
use GraphQL\Language\AST\OperationDefinitionNode;
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
            }
        }
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
