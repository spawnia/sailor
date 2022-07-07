<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests;

use Spawnia\PHPUnitAssertFiles\AssertDirectory;
use Spawnia\Sailor\Codegen\Generator;
use Spawnia\Sailor\Codegen\Writer;

final class Examples
{
    use AssertDirectory;

    public const EXAMPLES = [
        'custom-types',
        'input',
        'php-keywords',
        'polymorphic',
        'simple',
    ];

    public const EXAMPLES_PATH = __DIR__ . '/../examples';

    public static function assertGeneratesExpectedCode(string $example): void
    {
        self::generate($example);
        self::assertDirectoryEquals(self::expectedPath($example), self::generatedPath($example));
    }

    public static function generate(string $example): void
    {
        $examplePath = self::examplePath($example);
        $configFile = \Safe\realpath("{$examplePath}/sailor.php");

        $config = require $configFile;
        $endpoint = $config[$example];

        $generator = new Generator($endpoint, $configFile, $example);
        $files = $generator->generate();

        $writer = new Writer($endpoint);
        $writer->write($files);
    }

    private static function examplePath(string $example): string
    {
        return Examples::EXAMPLES_PATH . '/' . $example;
    }

    public static function expectedPath(string $example): string
    {
        return self::examplePath($example) . '/expected';
    }

    public static function generatedPath(string $example): string
    {
        return self::examplePath($example) . '/generated';
    }
}
