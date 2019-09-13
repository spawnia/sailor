<?php

declare(strict_types=1);

namespace Spawnia\Sailor;

/**
 * Subclasses of this class are automatically generated.
 *
 * They must implement the following abstract function:
 * public abstract function execute(mixed[] ...$args): mixed
 */
abstract class Operation
{
    /**
     * We expect this file to reside in vendor/sailor/src/Operation.php,
     * and assume the user to place a config file in the project root.
     */
    const EXPECTED_CONFIG_LOCATION = __DIR__.'/../../../sailor.php';

    /**
     * @var EndpointConfig[]|null
     */
    protected static $endpointConfigMap;

    /**
     * The configured endpoint the operation belongs to.
     *
     * @return string
     */
    abstract public static function endpoint(): string;

    /**
     * The GraphQL query string.
     *
     * @return string
     */
    abstract public static function document(): string;

    /**
     * Set the endpoint configuration.
     *
     * @param  EndpointConfig[]  $endpointConfigMap
     */
    public static function setEndpointConfigMap(array $endpointConfigMap): void
    {
        self::$endpointConfigMap = $endpointConfigMap;
    }

    // TODO pass variables
    protected static function fetchResponse(): Response
    {
        if (! self::$endpointConfigMap) {
            self::loadConfig();
        }

        $endpointConfig = self::$endpointConfigMap[static::endpoint()];

        $client = $endpointConfig->client();

        return $client->request(static::document());
    }

    protected static function loadConfig(): void
    {
        if (! file_exists(self::EXPECTED_CONFIG_LOCATION)) {
            throw new \Exception('Place a configuration file called sailor.php in your project root.');
        }

        self::$endpointConfigMap = include self::EXPECTED_CONFIG_LOCATION;
    }
}
