<?php declare(strict_types=1);

namespace Spawnia\Sailor;

final class Configuration
{
    /** @var array<string, array<string, EndpointConfig>> */
    public static array $configs;

    private function __construct() {}

    public static function endpoint(string $file, string $endpoint): EndpointConfig
    {
        $endpoints = self::endpoints($file);

        if (! isset($endpoints[$endpoint])) {
            throw ConfigurationException::missingEndpoint($endpoint);
        }

        return $endpoints[$endpoint];
    }

    /** @return array<string, \Spawnia\Sailor\EndpointConfig> */
    public static function endpoints(string $file): array
    {
        return self::$configs[$file] ??= self::loadConfig($file);
    }

    public static function setEndpoint(string $file, string $name, EndpointConfig $endpointConfig): void
    {
        self::$configs[$file][$name] = $endpointConfig;
    }

    /** @param class-string<BelongsToEndpoint> $belongsToEndpoint */
    public static function setEndpointFor(string $belongsToEndpoint, EndpointConfig $endpointConfig): void
    {
        self::$configs[$belongsToEndpoint::config()][$belongsToEndpoint::endpoint()] = $endpointConfig;
    }

    /** @return array<string, EndpointConfig> */
    private static function loadConfig(string $file): array
    {
        if (! file_exists($file)) {
            throw ConfigurationException::missingFile($file, false);
        }

        $config = require $file;
        assert(is_array($config), "Expected configuration file {$file} to return array of EndpointConfig");
        /** @var array<string, EndpointConfig> $config */

        return $config;
    }
}
