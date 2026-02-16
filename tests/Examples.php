<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests;

use Spawnia\PHPUnitAssertFiles\AssertDirectory;
use Spawnia\Sailor\Codegen\Generator;
use Spawnia\Sailor\Codegen\Writer;
use Spawnia\Sailor\EndpointConfig;

final class Examples
{
    use AssertDirectory;

    public const EXAMPLES = [
        'custom-types',
        'inline-fragments',
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
        assert(is_array($config));

        $endpoint = $config[$example];
        assert($endpoint instanceof EndpointConfig);

        $generator = new Generator($endpoint, $configFile, $example);
        $files = $generator->generate();

        $writer = new Writer($endpoint);
        $writer->write($files);
    }

    public static function examplePath(string $example): string
    {
        $basePath = Examples::EXAMPLES_PATH;

        return "{$basePath}/{$example}";
    }

    public static function expectedPath(string $example): string
    {
        $examplePath = self::examplePath($example);

        return "{$examplePath}/expected";
    }

    public static function generatedPath(string $example): string
    {
        $examplePath = self::examplePath($example);

        return "{$examplePath}/generated";
    }
}
