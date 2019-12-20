<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Error\Error;
use GraphQL\Error\FormattedError;
use GraphQL\Language\AST\DocumentNode;
use GraphQL\Type\Schema;
use GraphQL\Validator\DocumentValidator;

class Validator
{
    public static function validate(Schema $schema, DocumentNode $document): void
    {
        $errors = DocumentValidator::validate($schema, $document);

        if(count($errors) === 0) {
            return;
        }

        $formattedErrors = array_map(
            function(Error $error): array {
                return FormattedError::createFromException($error, true);
            },
            $errors
        );

        $errorStrings = array_map(
            function(array $error): string {
                return \Safe\json_encode($error);
            },
            $formattedErrors
        );

        throw new \Exception(
            implode("\n\n", $errorStrings)
        );
    }
}
