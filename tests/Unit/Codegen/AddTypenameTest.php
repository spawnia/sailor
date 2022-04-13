<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit\Codegen;

use GraphQL\Language\Parser;
use GraphQL\Language\Printer;
use Spawnia\Sailor\Codegen\AddTypename;
use Spawnia\Sailor\Tests\TestCase;

final class AddTypenameTest extends TestCase
{
    public function testSimpleQuery(): void
    {
        $document = Parser::parse(/** @lang GraphQL */ <<<'GRAPHQL'
            {
              simple
            }

            GRAPHQL
        );

        AddTypename::modify($document);

        self::assertSame(/** @lang GraphQL */ <<<'GRAPHQL'
            {
              __typename
              simple
            }

            GRAPHQL,
            Printer::doPrint($document)
        );
    }

    public function testInlineFragment(): void
    {
        $document = Parser::parse(/** @lang GraphQL */ <<<'GRAPHQL'
            {
              ... on Foo {
                inline
                __typename
              }
            }

            GRAPHQL
        );

        AddTypename::modify($document);

        self::assertSame(/** @lang GraphQL */ <<<'GRAPHQL'
            {
              __typename
              ... on Foo {
                inline
              }
            }

            GRAPHQL,
            Printer::doPrint($document)
        );
    }

    public function testNestedInlineFragment(): void
    {
        $document = Parser::parse(/** @lang GraphQL */ <<<'GRAPHQL'
            {
              ... on Foo {
                nested {
                    __typename
                }
                __typename
              }
            }

            GRAPHQL
        );

        AddTypename::modify($document);

        self::assertSame(/** @lang GraphQL */ <<<'GRAPHQL'
            {
              __typename
              ... on Foo {
                nested {
                  __typename
                }
              }
            }

            GRAPHQL,
            Printer::doPrint($document)
        );
    }
}
