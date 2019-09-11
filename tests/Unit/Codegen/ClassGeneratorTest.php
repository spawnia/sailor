<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit\Codegen;

use GraphQL\Language\Parser;
use GraphQL\Utils\BuildSchema;
use PHPUnit\Framework\TestCase;
use Spawnia\Sailor\Codegen\ClassGenerator;

class ClassGeneratorTest extends TestCase
{
    public function testGenerateSimple(): void
    {
        $schema = BuildSchema::build('
        type Query {
            foo: ID
        }  
        ');
        $generator = new ClassGenerator($schema, 'Foo');

        $document = Parser::parse('
        query Foo {
            foo
        }
        ');
        $operationsSets = $generator->generate($document);
        self::assertCount(1, $operationsSets);

        $fooOperation = $operationsSets[0];
        self::assertCount(1, $fooOperation->selectionStorage);
    }

    public function testGenerateNested(): void
    {
        $schema = BuildSchema::build('
        type Query {
            foo: Foo
        }

        type Foo {
            bar: Int
        }
        ');
        $generator = new ClassGenerator($schema, 'Foo');

        $document = Parser::parse('
        query Foo {
            foo {
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
}
