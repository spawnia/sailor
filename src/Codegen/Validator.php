<?php declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Error\DebugFlag;
use GraphQL\Error\Error;
use GraphQL\Error\FormattedError;
use GraphQL\Language\AST\DocumentNode;
use GraphQL\Type\Schema;
use GraphQL\Validator\DocumentValidator;

class Validator
{
    public static function validate(Schema $schema, DocumentNode $document): void
    {
        try {
            $errors = DocumentValidator::validate($schema, $document);
        } catch (\Throwable $e) {
            throw new \Exception(
                'Unexpected error while validating a query against the schema. Check if your schema is up to date.',
                0,
                $e
            );
        }

        if (0 === count($errors)) {
            return;
        }

        $formattedErrors = array_map(
            static function (Error $error): array {
                return FormattedError::createFromException($error, DebugFlag::INCLUDE_DEBUG_MESSAGE);
            },
            $errors
        );

        $errorStrings = array_map(
            static function (array $error): string {
                return \Safe\json_encode($error);
            },
            $formattedErrors
        );

        throw new \Exception(
            implode("\n\n", $errorStrings)
        );
    }
}
