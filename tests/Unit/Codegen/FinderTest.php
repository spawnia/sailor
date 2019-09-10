<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit\Codegen;

use PHPUnit\Framework;
use Spawnia\Sailor\Codegen\Finder;

class FinderTest extends Framework\TestCase
{
    public function testGreetIncludesName(): void
    {
        $finder = new Finder(__DIR__.'/../../examples/foo');
        $files = $finder->documents();

        $first = reset($files);
        self::assertStringEqualsFile(
            __DIR__.'/../../examples/foo/foo.graphql',
            $first
        );
    }
}
