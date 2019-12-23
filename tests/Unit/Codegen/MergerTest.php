<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit\Codegen;

use GraphQL\Language\Parser;
use PHPUnit\Framework\TestCase;
use Spawnia\Sailor\Codegen\Merger;

class MergerTest extends TestCase
{
    public function testCombine(): void
    {
        $foo = Parser::parse('
        query MyScalarQuery {
            simple
        }
        ');
        $bar = Parser::parse('
        query Bar {
            bar
        }
        ');

        $merged = Merger::combine(['simple' => $foo, 'bar' => $bar]);

        self::assertCount(2, $merged->definitions);
    }
}
