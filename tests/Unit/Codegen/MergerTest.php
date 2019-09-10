<?php

namespace Spawnia\Sailor\Tests\Unit\Codegen;

use GraphQL\Language\Parser;
use Spawnia\Sailor\Codegen\Merger;
use PHPUnit\Framework\TestCase;

class MergerTest extends TestCase
{
    public function testCombine()
    {
        $foo = Parser::parse('
        query Foo {
            foo
        }
        ');
        $bar = Parser::parse('
        query Bar {
            bar
        }
        ');
        $merged = Merger::combine([$foo, $bar]);
        self::assertCount(2, $merged->definitions);
    }
}
