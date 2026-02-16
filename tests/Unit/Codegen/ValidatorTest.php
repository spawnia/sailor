<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit\Codegen;

use GraphQL\Language\Parser;
use GraphQL\Utils\BuildSchema;
use Spawnia\Sailor\Codegen\Validator;
use Spawnia\Sailor\Tests\TestCase;

final class ValidatorTest extends TestCase
{
    public function testValidateSuccess(): void
    {
        self::expectNotToPerformAssertions();

        $schema = BuildSchema::build(/** @lang GraphQL */ '
        type Query {
            simple: ID
        }
        ');

        $document = Parser::parse(/** @lang GraphQL */ '
        {
            simple
        }
        ');
        Validator::validateDocumentWithSchema($schema, $document);
    }

    public function testValidateFailure(): void
    {
        $schema = BuildSchema::build(/** @lang GraphQL */ '
        type Query {
            simple: ID
        }
        ');

        $document = Parser::parse(/** @lang GraphQL */ '
        {
            bar
        }
        ');

        $this->expectException(\Exception::class);
        Validator::validateDocumentWithSchema($schema, $document);
    }

    public function testValidateDocumentsPasses(): void
    {
        self::expectNotToPerformAssertions();

        Validator::validateDocuments([
            'simple' => Parser::parse(/* @lang GraphQL */ <<<'GRAPHQL'
            query Name {
                simple
            }
            GRAPHQL),
        ]);
    }

    public function testValidateDocumentsUnnamedOperation(): void
    {
        $path = 'thisShouldBeInTheMessage';
        $document = Parser::parse(/* @lang GraphQL */ <<<'GRAPHQL'
        {
            unnamedQuery
        }
        GRAPHQL);

        self::expectExceptionMessage("Found unnamed operation definition in {$path}.");
        Validator::validateDocuments([
            $path => $document,
        ]);
    }

    public function testValidateDocumentsLowercaseOperation(): void
    {
        $path = 'thisShouldBeInTheMessage';
        $name = 'camelCase';
        $document = Parser::parse(/* @lang GraphQL */ <<<GRAPHQL
        query {$name} {
            field
        }
        GRAPHQL);

        self::expectExceptionMessage("Operation names must be PascalCase, found {$name} in {$path}.");
        Validator::validateDocuments([
            $path => $document,
        ]);
    }
}
