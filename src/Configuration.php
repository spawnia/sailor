<?php

declare(strict_types=1);

namespace Spawnia\Sailor;

use Spawnia\Sailor\Console\ConsoleException;

class Configuration
{
    /**
     * We expect this file to reside in vendor/sailor/src/Operation.php,
     * and expect users to place a config file in the project root.
     */
    public const EXPECTED_CONFIG_LOCATION = __DIR__.'/../../../../sailor.php';

    /**
     * Since loading the config is a bit expensive and might happen
     * often, the result is cached here. Make sure to always call.
     * @see Configuration::ensureEndpointsAreLoaded() before accessing this.
     *
     * @var array<string, \Spawnia\Sailor\EndpointConfig>
     */
    protected static array $endpoints;

    public static function endpoint(string $name): EndpointConfig
    {
        self::ensureEndpointsAreLoaded();

        if (! isset(self::$endpoints[$name])) {
            throw ConsoleException::missingEndpoint($name);
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
                    __DIR__.'/../sailor.php',
                    self::EXPECTED_CONFIG_LOCATION
                );

                echo <<<'EOF'
    Sailor requires a configuration file to run.

    Created an example configuration "sailor.php" in your project root.
    Modify it to your needs and try again.

    EOF;
                exit(1);
            }

            $endpoints = include self::EXPECTED_CONFIG_LOCATION;
            if (! is_array($endpoints)) {
                throw new ConfigurationException('Expected config file at '.self::EXPECTED_CONFIG_LOCATION.' to return an array.');
            }

            self::$endpoints = $endpoints;
        }
    }
}
