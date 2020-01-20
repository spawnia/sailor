<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit\Codegen;

use GraphQL\Language\Parser;
use GraphQL\Utils\BuildSchema;
use PHPUnit\Framework\TestCase;
use Spawnia\Sailor\Codegen\ClassGenerator;
use Spawnia\Sailor\Testing\MockEndpointConfig;

class ClassGeneratorTest extends TestCase
{
    public function testGenerateSimple(): void
    {
        $schema = BuildSchema::build(/** @lang GraphQL */ '
        type Query {
            simple: ID
        }
        ');
        $generator = new ClassGenerator(
            $schema,
            $this->mockEndpoint('MyScalarQuery'),
            'simple'
        );

        $document = Parser::parse(/** @lang GraphQL */ '
        query MyScalarQuery {
            simple
        }
        ');
        $operationsSets = $generator->generate($document);
        self::assertCount(1, $operationsSets);

        $fooOperation = $operationsSets[0];
        self::assertCount(1, $fooOperation->selectionStorage);
    }

    public function testGenerateNested(): void
    {
        $schema = BuildSchema::build(/** @lang GraphQL */ '
        type Query {
            simple: MyScalarQuery
        }

        type MyScalarQuery {
            bar: Int
        }
        ');
        $generator = new ClassGenerator(
            $schema,
            $this->mockEndpoint('MyScalarQuery'),
            'simple'
        );

        $document = Parser::parse(/** @lang GraphQL */ '
        query MyScalarQuery {
            simple {
                bar
            }
        }
        ');
        $operationsSets = $generator->generate($document);
        self::assertCount(1, $operationsSets);

        $fooOperation = $operationsSets[0];
        $selections = $fooOperation->selectionStorage;
        self::assertCount(2, $selections);
    }

    public function testGenerateEnum(): void
    {
        $schema = BuildSchema::build(/** @lang GraphQL */ '
        type Query {
            simple: MyScalarQuery
        }

        enum MyScalarQuery {
            BAR
        }
        ');
        $generator = new ClassGenerator(
            $schema,
            $this->mockEndpoint('MyScalarQuery'),
            'simple'
        );

        $document = Parser::parse(/** @lang GraphQL */ '
        query MyScalarQuery {
            simple
        }
        ');
        $operationsSets = $generator->generate($document);
        self::assertCount(1, $operationsSets);

        $fooOperation = $operationsSets[0];
        $selections = $fooOperation->selectionStorage;
        self::assertCount(1, $selections);
    }

    protected function mockEndpoint(string $namespace): MockEndpointConfig
    {
        $endpoint = new MockEndpointConfig();
        $endpoint->namespace = $namespace;

        return $endpoint;
    }
}
