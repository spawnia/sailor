<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Integration;

use Spawnia\Sailor\Foo\Foo;
use Spawnia\Sailor\Operation;
use PHPUnit\Framework\TestCase;
use Spawnia\Sailor\Codegen\Generator;
use Spawnia\Sailor\Codegen\GeneratorOptions;

class FooTest extends TestCase
{
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

        self::assertTrue(is_a(Foo::class, Operation::class, true));
    }
}
