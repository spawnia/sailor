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
                query Foo {
                    foo
                }
            ',
        ];

        $parsed = Generator::parseDocuments($documents);
        self::assertSame('Foo', $parsed['path']->definitions[0]->name->value);
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
            'foo' => Parser::parse(/* @lang GraphQL */ '
            query Name {
                foo
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
