<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit\Codegen;

use GraphQL\Language\Parser;
use PHPUnit\Framework\TestCase;
use Spawnia\Sailor\Codegen\Merger;

class MergerTest extends TestCase
{
    public function testCombine(): void
    {
        $foo = Parser::parse(/** @lang GraphQL */ '
        query MyScalarQuery {
            simple
        }
        ');
        $bar = Parser::parse(/** @lang GraphQL */ '
        query Bar {
            bar
        }
        ');

        $merged = Merger::combine(['simple' => $foo, 'bar' => $bar]);
        $definitions = $merged->definitions;

        self::assertCount(2, $definitions);
        self::assertSame($definitions['MyScalarQuery'], $foo->definitions[0]);
        self::assertSame($definitions['Bar'], $bar->definitions[0]);
    }
}
