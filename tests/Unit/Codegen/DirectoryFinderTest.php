<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit\Codegen;

use Spawnia\Sailor\Codegen\DirectoryFinder;
use Spawnia\Sailor\Tests\TestCase;
use function uniqid;

final class DirectoryFinderTest extends TestCase
{
    public function testFindsFiles(): void
    {
        $finder = new DirectoryFinder(__DIR__ . '/finder');

        self::assertCount(3, $finder->documents());
    }

    public function testCreatesDirIfNotExists(): void
    {
        $finder = new DirectoryFinder(__DIR__ . '/' . uniqid('finder-'));

        self::assertCount(0, $finder->documents());
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
