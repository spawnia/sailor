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
            'path' => /* @lang GraphQL */ '
                query MyScalarQuery {
                    simple
                }
            ',
        ];

        $parsed = Generator::parseDocuments($documents);
        /** @var \GraphQL\Language\AST\OperationDefinitionNode $query */
        $query = $parsed['path']->definitions[0];

        self::assertSame('MyScalarQuery', $query->name->value);
    }

    public function testParseDocumentsThrowsErrorWithPath(): void
    {
        $path = 'thisShouldBeInTheMessage';
        $documents = [
            $path => /* @lang GraphQL */
                'invalid GraphQL',
        ];

        self::expectExceptionMessageMatches("/$path/");
        Generator::parseDocuments($documents);
    }

    public function testEnsureOperationsAreNamedPasses(): void
    {
        self::expectNotToPerformAssertions();
        $documents = [
            'simple' => Parser::parse(/* @lang GraphQL */ '
            query Name {
                simple
            }
            '),
        ];

        Generator::ensureOperationsAreNamed($documents);
    }

    public function testEnsureOperationsAreNamedThrowsErrorWithPath(): void
    {
        $path = 'thisShouldBeInTheMessage';
        $documents = [
            $path => Parser::parse(/* @lang GraphQL */ '
            {
                unnamedQuery
            }
            '),
        ];

        self::expectExceptionMessageMatches("/$path/");
        Generator::ensureOperationsAreNamed($documents);
    }
}
