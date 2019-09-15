<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit\Codegen;

use PHPUnit\Framework;
use Spawnia\Sailor\Codegen\Finder;

class FinderTest extends Framework\TestCase
{
    public function testFindsSingleFile(): void
    {
        $rootPath = __DIR__.'/../../../examples/foo';
        $finder = new Finder($rootPath);
        $files = $finder->documents();

        self::assertCount(1, $files);
        self::assertStringEqualsFile(
            $rootPath.'/foo.graphql',
            $files[0]
        );
    }
}
