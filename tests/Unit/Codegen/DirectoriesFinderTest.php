<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit\Codegen;

use Spawnia\Sailor\Codegen\DirectoriesFinder;
use Spawnia\Sailor\Codegen\DirectoryFinder;
use Spawnia\Sailor\Tests\TestCase;

final class DirectoriesFinderTest extends TestCase
{
    public function testCombinesDirectoryFinders(): void
    {
        $suffixFinder = new DirectoryFinder(__DIR__ . '/finder', '/^.+\.suffix\.graphql$/');
        $subFinder = new DirectoryFinder(__DIR__ . '/finder/sub');

        $directoriesFinder = new DirectoriesFinder([$suffixFinder, $subFinder]);

        self::assertSame([
            __DIR__ . '/finder/custom.suffix.graphql',
            __DIR__ . '/finder/sub/should-also-be-found.graphql',
        ], array_keys($directoriesFinder->documents()));
    }

    public function testDeduplicates(): void
    {
        $subFinder1 = new DirectoryFinder(__DIR__ . '/finder/sub');
        $subFinder2 = new DirectoryFinder(__DIR__ . '/finder/sub');

        $directoriesFinder = new DirectoriesFinder([$subFinder1, $subFinder2]);

        self::assertSame([
            __DIR__ . '/finder/sub/should-also-be-found.graphql',
        ], array_keys($directoriesFinder->documents()));
    }
}
