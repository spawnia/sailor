<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Integration;

use Spawnia\PHPUnitAssertFiles\AssertDirectory;
use Spawnia\Sailor\Codegen\Generator;
use Spawnia\Sailor\Codegen\Writer;
use Spawnia\Sailor\Tests\TestCase;

final class CodegenTest extends TestCase
{
    use AssertDirectory;

    public const EXAMPLES_PATH = __DIR__ . '/../../examples';

    /**
     * @dataProvider examples
     */
    public function testGeneratesExpectedCode(string $example): void
    {
        $examplePath = self::EXAMPLES_PATH . '/' . $example;
        $configFile = "{$examplePath}/sailor.php";

        $config = require $configFile;
        $endpoint = $config[$example];

        $generator = new Generator($endpoint, $configFile, $example);
        $files = $generator->generate();

        $writer = new Writer($endpoint);
        $writer->write($files);

        self::assertDirectoryEquals("{$examplePath}/expected", "{$examplePath}/generated");
    }

    /**
     * @return iterable<array{string}>
     */
    public static function examples(): iterable
    {
        yield ['custom-types'];
        yield ['input'];
        yield ['simple'];
        yield ['polymorphic'];
    }
}
