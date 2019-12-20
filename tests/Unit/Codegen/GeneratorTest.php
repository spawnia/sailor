<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit\Codegen;

use GraphQL\Language\AST\DocumentNode;
use GraphQL\Language\Parser;
use PHPUnit\Framework\TestCase;
use Spawnia\Sailor\Codegen\Generator;

class GeneratorTest extends TestCase
{
    public function testParseDocumentsSuccessfully(): void
    {
        $documents = [
            'path' => /** @lang GraphQL */ '
                query Foo {
                    foo
                }
            ',
        ];

        $parsed = Generator::parseDocuments($documents);
        self::assertInstanceOf(DocumentNode::class, $parsed['path']);
    }

    public function testParseDocumentsThrowsErrorWithPath(): void
    {
        $path = 'thisShouldBeInTheMessage';
        $documents = [
            $path => /** @lang GraphQL */
                'invalid GraphQL',
        ];

        $this->expectExceptionMessageMatches("/$path/");
        Generator::parseDocuments($documents);
    }

    public function testEnsureOperationsAreNamedPasses(): void
    {
        $documents = [
            'foo' => Parser::parse(/** @lang GraphQL */ '
            query Name {
                foo
            }
            '),
        ];

        Generator::ensureOperationsAreNamed($documents);
        self::assertTrue(true);
    }

    public function testEnsureOperationsAreNamedThrowsErrorWithPath(): void
    {
        $path = 'thisShouldBeInTheMessage';
        $documents = [
            $path => Parser::parse(/** @lang GraphQL */ '
            {
                unnamedQuery
            }
            '),
        ];

        $this->expectExceptionMessageMatches("/$path/");
        Generator::ensureOperationsAreNamed($documents);
    }
}
