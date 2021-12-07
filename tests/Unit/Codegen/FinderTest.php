<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit\Codegen;

use PHPUnit\Framework;
use Spawnia\Sailor\Codegen\Finder;

class FinderTest extends Framework\TestCase
{
    public function testFindsFiles(): void
    {
        $finder = new Finder(__DIR__ . '/finder');

        self::assertCount(2, $finder->documents());
    }

    public function testNoFiles(): void
    {
        $finder = new Finder(__DIR__ . '/finder/empty');

        self::assertCount(0, $finder->documents());
    }
}
