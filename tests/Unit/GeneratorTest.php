<?php

namespace Spawnia\Sailor\Tests\Unit;

use GraphQL\Language\Parser;
use GraphQL\Utils\BuildSchema;
use Spawnia\Sailor\Generator;
use PHPUnit\Framework\TestCase;

class GeneratorTest extends TestCase
{
    public function testGenerate(): void
    {
        $schema = BuildSchema::build('
        type Query {
            foo: ID
        }  
        ');
        $generator = new Generator($schema, 'Foo');

        $document = Parser::parse('
        {
            foo
        }
        ', [
            'noLocations' => true,
        ]);
        $generator->generate($document);
    }
}
