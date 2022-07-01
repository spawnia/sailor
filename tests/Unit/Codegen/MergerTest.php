<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit\Codegen;

use GraphQL\Language\Parser;
use Spawnia\Sailor\Codegen\Merger;
use Spawnia\Sailor\Tests\TestCase;

final class MergerTest extends TestCase
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

        self::assertCount(2, $merged->definitions);
    }
}
