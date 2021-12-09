<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit\Codegen;

use GraphQL\Error\Error;
use GraphQL\Language\AST\NameNode;
use GraphQL\Language\AST\OperationDefinitionNode;
use GraphQL\Language\Parser;
use PHPUnit\Framework\TestCase;
use Spawnia\Sailor\Codegen\Generator;

class GeneratorTest extends TestCase
{
    public function testParseNamedOperationSuccessfully(): void
    {
        $somePath = 'path';
        $documents = [
            $somePath => /* @lang GraphQL */ '
                query MyScalarQuery {
                    simple
                }
            ',
        ];

        $parsed = Generator::parseDocuments($documents);
        self::assertCount(1, $parsed);

        $definitions = $parsed[$somePath]->definitions;
        self::assertCount(1, $definitions);

        $query = $definitions[0];
        self::assertInstanceOf(OperationDefinitionNode::class, $query);

        $nameNode = $query->name;
        self::assertInstanceOf(NameNode::class, $nameNode);
        self::assertSame('MyScalarQuery', $nameNode->value);
    }

    public function testParseFragmentSuccessfully(): void
    {
        $somePath = 'path';
        $documents = [
            $somePath => /* @lang GraphQL */ '
                fragment Foo on Bar {
                    simple
                }
            ',
        ];

        $parsed = Generator::parseDocuments($documents);
        /** @var \GraphQL\Language\AST\FragmentDefinitionNode $fragment */
        $fragment = $parsed[$somePath]->definitions[0];

        self::assertSame('Foo', $fragment->name->value);
    }

    public function testParseOperationsAndFragmentsSuccessfully(): void
    {
        $somePath = 'path';
        $documents = [
            $somePath => /* @lang GraphQL */ '
                query FooQuery {
                    ...Foo
                }

                fragment Foo on Bar {
                    simple
                }
            ',
        ];

        $parsed = Generator::parseDocuments($documents);
        /** @var \GraphQL\Language\AST\OperationDefinitionNode $query */
        $query = $parsed[$somePath]->definitions[0];

        self::assertSame('FooQuery', $query->name->value);

        /** @var \GraphQL\Language\AST\FragmentDefinitionNode $fragment */
        $fragment = $parsed[$somePath]->definitions[1];

        self::assertSame('Foo', $fragment->name->value);
    }

    public function testEmptyListOfDocuments(): void
    {
        self::assertSame([], Generator::parseDocuments([]));
    }

    public function testParseDocumentsThrowsErrorWithPath(): void
    {
        $path = 'thisShouldBeInTheMessage';
        $documents = [
            $path /* @lang GraphQL */ => 'invalid GraphQL',
        ];

        self::expectExceptionMessageMatches("/$path/");
        Generator::parseDocuments($documents);
    }

    public function testFailsOnNonExecutableDefinitions(): void
    {
        $somePath = 'path';
        $documents = [
            $somePath => Parser::parse(/* @lang GraphQL */ '
                type Query {
                    foo: ID
                }
            '),
        ];

        $this->expectException(Error::class);
        Generator::validateDocuments($documents);
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

        Generator::validateDocuments($documents);
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
        Generator::validateDocuments($documents);
    }
}
