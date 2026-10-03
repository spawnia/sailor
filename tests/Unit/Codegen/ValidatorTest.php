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

    /** @return iterable<string, array{string, string}> */
    public static function clientDirectiveLocations(): iterable
    {
        yield 'field skip' => ['query Foo { bar @skip(if: true) }', '@skip on field bar in operation Foo in some.graphql'];
        yield 'field include' => ['query Foo($v: Boolean!) { bar @include(if: $v) }', '@include on field bar in operation Foo in some.graphql'];
        yield 'literal false' => ['query Foo { bar @skip(if: false) }', '@skip on field bar in operation Foo in some.graphql'];
        yield 'nested field' => ['query Foo { bar { baz @skip(if: true) } }', '@skip on field baz in operation Foo in some.graphql'];
        yield 'inline fragment' => ['query Foo { ... on Query @skip(if: true) { bar } }', '@skip on inline fragment on Query in operation Foo in some.graphql'];
        yield 'fragment spread' => ['query Foo { ...Bar @include(if: true) }', '@include on fragment spread ...Bar in operation Foo in some.graphql'];
        yield 'inside fragment definition' => ['fragment Bar on Query { bar @skip(if: true) }', '@skip on field bar in fragment Bar in some.graphql'];
    }

    /** @dataProvider clientDirectiveLocations */
    public function testValidateDocumentsRejectsClientDirectives(string $source, string $message): void
    {
        self::expectExceptionMessage("Sailor does not support client directives, found {$message}.");
        Validator::validateDocuments([
            'some.graphql' => Parser::parse($source),
        ]);
    }

    public function testValidateDocumentsAllowsOtherDirectives(): void
    {
        self::expectNotToPerformAssertions();

        Validator::validateDocuments([
            'some.graphql' => Parser::parse('query Foo { bar @deprecated }'),
        ]);
    }
}
