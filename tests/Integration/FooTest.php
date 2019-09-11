<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Spawnia\Sailor\Codegen\Generator;
use Spawnia\Sailor\Codegen\GeneratorOptions;
use Spawnia\PHPUnitAssertFiles\AssertDirectory;

class FooTest extends TestCase
{
    use AssertDirectory;

    const EXAMPLES_PATH = __DIR__.'/../../examples/foo/';

    public function testGeneratesFooExample(): void
    {
        $options = new GeneratorOptions();
        $options->namespace = 'Spawnia\\Sailor\\Foo';
        $options->searchPath = self::EXAMPLES_PATH;
        $options->targetPath = self::EXAMPLES_PATH.'generated';
        $options->schemaPath = self::EXAMPLES_PATH.'schema.graphqls';

        $generator = new Generator($options);
        $generator->run();

        self::assertDirectoryEquals(self::EXAMPLES_PATH.'expected', self::EXAMPLES_PATH.'generated');
    }
}
