<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit\Codegen;

use GraphQL\Language\Parser;
use GraphQL\Language\Printer;
use PHPUnit\Framework\TestCase;
use Spawnia\Sailor\Codegen\AddTypename;

class AddTypenameTest extends TestCase
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
              simple
              __typename
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
              ... on Foo {
                inline
              }
              __typename
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
              ... on Foo {
                nested {
                  __typename
                }
              }
              __typename
            }

            GRAPHQL,
            Printer::doPrint($document)
        );
    }
}
