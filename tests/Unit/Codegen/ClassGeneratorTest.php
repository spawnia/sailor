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
        $generator = $this->createTestGenerator(/** @lang GraphQL */ '
        type Query {
            simple: ID
        }
        ');

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

    public function testGenerateWithFragment(): void
    {
        $generator = $this->createTestGenerator(/** @lang GraphQL */ '
        type Query {
            simple: ID
        }
        ');

        $document = Parser::parse(/** @lang GraphQL */ '
        query MyScalarQuery {
            ...MyFragment
        }

        fragment MyFragment on Query {
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
        $generator = $this->createTestGenerator(/** @lang GraphQL */ '
        type Query {
            simple: MyScalarQuery
        }

        type MyScalarQuery {
            bar: Int
        }
        ');

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
        $generator = $this->createTestGenerator(/** @lang GraphQL */ '
        type Query {
            simple: MyScalarQuery
        }

        enum MyScalarQuery {
            BAR
        }
        ');

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

    protected function createTestGenerator(string $schema): ClassGenerator
    {
        $endpoint = new MockEndpointConfig();
        $endpoint->namespace = 'TestNamespace';

        return new ClassGenerator(
            BuildSchema::build($schema),
            $endpoint,
            'endpointName'
        );
    }
}
