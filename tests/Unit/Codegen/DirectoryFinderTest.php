<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit\Codegen;

use PHPUnit\Framework;
use Spawnia\Sailor\Codegen\DirectoryFinder;

class DirectoryFinderTest extends Framework\TestCase
{
    public function testFindsFiles(): void
    {
        $finder = new DirectoryFinder(__DIR__ . '/finder');

        self::assertCount(3, $finder->documents());
    }

    public function testNoFiles(): void
    {
        $finder = new DirectoryFinder(__DIR__ . '/finder/empty');

        self::assertCount(0, $finder->documents());
    }

    public function testPattern(): void
    {
        $finder = new DirectoryFinder(__DIR__ . '/finder', '/^.+\.suffix\.graphql$/');

        self::assertCount(1, $finder->documents());
    }
}
