<?php declare(strict_types=1);

namespace Spawnia\Sailor;

class Configuration
{
    /**
     * We expect this file to reside in vendor/sailor/src/Operation.php,
     * and expect users to place a config file in the project root.
     */
    public const EXPECTED_CONFIG_LOCATION = __DIR__ . '/../../../../sailor.php';

    /**
     * Loading the config is expensive and might happen repeatedly, so it is cached.
     * Make sure to always call @see Configuration::ensureEndpointsAreLoaded() before accessing this.
     *
     * @var array<string, \Spawnia\Sailor\EndpointConfig>
     */
    protected static array $endpoints;

    public static function endpoint(string $name): EndpointConfig
    {
        self::ensureEndpointsAreLoaded();

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
        self::ensureEndpointsAreLoaded();

        return self::$endpoints;
    }

    public static function setEndpoint(string $name, EndpointConfig $endpointConfig): void
    {
        self::$endpoints[$name] = $endpointConfig;
    }

    protected static function ensureEndpointsAreLoaded(): void
    {
        if (! isset(self::$endpoints)) {
            if (! file_exists(self::EXPECTED_CONFIG_LOCATION)) {
                \Safe\copy(
                    __DIR__ . '/../sailor.php',
                    self::EXPECTED_CONFIG_LOCATION
                );

                echo <<<'EOF'
                    Sailor requires a configuration file to run.

                    Created an example configuration "sailor.php" in your project root.
                    Modify it to your needs and try again.

                    EOF;
                exit(1);
            }

            $endpoints = require self::EXPECTED_CONFIG_LOCATION;
            if (! is_array($endpoints)) {
                ConfigurationException::wrongReturnType(gettype($endpoints));
            }

            self::$endpoints = $endpoints;
        }
    }
}
