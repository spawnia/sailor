<?php

declare(strict_types=1);

namespace Spawnia\Sailor;

use Spawnia\Sailor\Console\ConsoleException;

class Configuration
{
    /**
     * We expect this file to reside in vendor/sailor/src/Operation.php,
     * and assume the user to place a config file in the project root.
     */
    public const EXPECTED_CONFIG_LOCATION = __DIR__.'/../../../../sailor.php';

    /**
     * Since loading the config is a bit expensive and might happen
     * often, the result is cached here. Make sure to always call.
     * @see Configuration::loadConfigIfNotExists() before accessing this.
     *
     * @var array<string, \Spawnia\Sailor\EndpointConfig>
     */
    public static array $endpointConfigMap;

    public static function assertConfigFileExists(): void
    {
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
    }

    /**
     * @param  array<string, \Spawnia\Sailor\EndpointConfig>  $endpointConfigMap
     */
    public static function setEndpointConfigMap(array $endpointConfigMap): void
    {
        self::$endpointConfigMap = $endpointConfigMap;
    }

    public static function forEndpoint(string $endpoint): EndpointConfig
    {
        self::loadConfigIfNotExists();

        if (! isset(self::$endpointConfigMap[$endpoint])) {
            throw ConsoleException::missingEndpoint($endpoint);
        }

        return self::$endpointConfigMap[$endpoint];
    }

    /**
     * @return array<string, \Spawnia\Sailor\EndpointConfig>
     */
    public static function getEndpointConfigMap(): array
    {
        self::loadConfigIfNotExists();

        return self::$endpointConfigMap;
    }

    protected static function loadConfigIfNotExists(): void
    {
        if (! isset(self::$endpointConfigMap)) {
            self::assertConfigFileExists();

            // The config should return an array
            self::$endpointConfigMap = include self::EXPECTED_CONFIG_LOCATION;
        }
    }
}
