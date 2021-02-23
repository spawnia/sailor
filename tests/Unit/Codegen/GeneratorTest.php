<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit\Codegen;

use GraphQL\Language\Parser;
use PHPUnit\Framework\TestCase;
use Spawnia\Sailor\Codegen\Generator;

class GeneratorTest extends TestCase
{
    public function testParseDocumentsSuccessfully(): void
    {
        $documents = [
            'path' => <<<GRAPHQL
                query MyScalarQuery {
                    simple
                }
            GRAPHQL,
        ];

        $parsed = Generator::parseDocuments($documents);
        /** @var \GraphQL\Language\AST\OperationDefinitionNode $query */
        $query = $parsed['path']->definitions[0];

        self::assertSame('MyScalarQuery', $query->name->value);
    }

    public function testEmptyListOfDocuments(): void
    {
        self::assertSame([], Generator::parseDocuments([]));
    }

    public function testParseDocumentsThrowsErrorWithPath(): void
    {
        $path = 'thisShouldBeInTheMessage';
        $documents = [
            $path => <<<GRAPHQL
            invalid GraphQL
            GRAPHQL
        ];

        self::expectExceptionMessageMatches("/$path/");
        Generator::parseDocuments($documents);
    }

    public function testEnsureOperationsAreNamedPasses(): void
    {
        self::expectNotToPerformAssertions();
        $documents = [
            'simple' => Parser::parse(<<<GRAPHQL
            query Name {
                simple
            }
            GRAPHQL
            ),
        ];

        Generator::ensureOperationsAreNamed($documents);
    }

    public function testEnsureOperationsAreNamedThrowsErrorWithPath(): void
    {
        $path = 'thisShouldBeInTheMessage';
        $documents = [
            $path => Parser::parse(<<<GRAPHQL
            {
                unnamedQuery
            }
            GRAPHQL
            ),
        ];

        self::expectExceptionMessageMatches("/$path/");
        Generator::ensureOperationsAreNamed($documents);
    }
}
