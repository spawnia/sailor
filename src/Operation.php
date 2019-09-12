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
     * @var EndpointConfig[]|null
     */
    protected static $endpointConfigMap;

    /**
     * The configured endpoint the operation belongs to.
     *
     * @return string
     */
    abstract protected function endpoint(): string;

    /**
     * The GraphQL query string.
     *
     * @return string
     */
    abstract protected function document(): string;

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
    protected function runInternal()
    {
        if (! self::$endpointConfigMap) {
            $this->loadConfig();
        }

        $endpointConfig = self::$endpointConfigMap[$this->endpoint()];

        $client = $endpointConfig->client();
        $response = $client->request($this->document());

        return Decoder::into($response, $this->getResultClassName());
    }

    private function loadConfig(): void
    {
        if (! file_exists(self::EXPECTED_CONFIG_LOCATION)) {
            throw new \Exception('Place a configuration file called sailor.php in your project root.');
        }

        self::$endpointConfigMap = include self::EXPECTED_CONFIG_LOCATION;
    }

    private function getResultClassName(): string
    {
        // Start with the FQCN of the child, e.g. Vendor\Generated\FooQuery
        return static::class
            // Add the name of the class itself as a namespace, e.g. \FooQuery
            .'\\'.get_class($this)
            // Finally add the expected name of the result class, e.g. \FooQueryResult
            // so we end up with Vendor\Generated\FooQuery\FooQuery\FooQueryResult
            .'\\'.get_class($this).'Result';
    }
}
