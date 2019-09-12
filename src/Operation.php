<?php

declare(strict_types=1);

namespace Spawnia\Sailor;

/**
 * Subclasses of this class are automatically generated.
 *
 * They must implement the following abstract function:
 * public abstract function run(mixed[] ...$args): mixed
 */
abstract class Operation
{
    /**
     * We expect this file to reside in vendor/sailor/src/Operation.php,
     * and assume the user to place a config file in the project root.
     */
    const EXPECTED_CONFIG_LOCATION = __DIR__.'/../../../sailor.php';

    /**
     * @var EndpointConfiguration[]|null
     */
    protected static $config;

    protected function runInternal(string $endpoint, string $document, \stdClass $variables = null)
    {
        if (! self::$config) {
            $this->loadConfig();
        }

        $endpointConfig = self::$config[$endpoint];

        $client = $endpointConfig->client();
        $response = $client->request($document, $variables);

        return $this->decode($response);
    }

    public static function setClientConfiguration(array $clientConfiguration): void
    {
        self::$config = $clientConfiguration;
    }

    private function loadConfig(): void
    {
        if (! file_exists(self::EXPECTED_CONFIG_LOCATION)) {
            throw new \Exception('Place a configuration file called sailor.php in your project root.');
        }

        self::$config = include self::EXPECTED_CONFIG_LOCATION;
    }

    private function decode(Response $response)
    {
        $class = $this->resultClass();
    }
}
