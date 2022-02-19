<?php declare(strict_types=1);

namespace Spawnia\Sailor;

use SplFileInfo;

final class Configuration
{
    public function __construct(SplFileInfo $configurationFile)
    {
        $filePath = $configurationFile->getRealPath();
        if (false === $filePath) {
            \Safe\copy(
                __DIR__ . '/../sailor.php',
                $configurationFile->getPath()
            );

            echo <<<'EOF'
                    Sailor requires a configuration file to run.

                    Created an example configuration "sailor.php" in your project root.
                    Modify it to your needs and try again.

                    EOF;
            exit(1);
        }

        self::$endpoints = require $filePath;
    }

    /**
     * @var array<string, \Spawnia\Sailor\EndpointConfig>
     */
    private static array $endpoints;

    public static function endpoint(string $name): EndpointConfig
    {
        if (! isset(self::$endpoints[$name])) {
            throw ConfigurationException::missingEndpoint($name);
        }

        return self::$endpoints[$name];
    }

    /**
     * @return array<string, \Spawnia\Sailor\EndpointConfig>
     */
    public static function endpoints(): array
    {
        return self::$endpoints;
    }

    public static function setEndpoint(string $name, EndpointConfig $endpointConfig): void
    {
        self::$endpoints[$name] = $endpointConfig;
    }
}
