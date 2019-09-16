<?php

declare(strict_types=1);

namespace Spawnia\Sailor;

use Spawnia\Sailor\Testing\MockClient;

abstract class EndpointConfig
{
    /** @var MockClient|null */
    public $mockClient;

    /**
     * Instantiate a client that will resolve the GraphQL operations.
     *
     * @return Client
     */
    abstract public function makeClient(): Client;

    /**
     * The namespace the generated classes will be created in.
     *
     * @return string
     */
    abstract public function namespace(): string;

    /**
     * Path to the directory where the generated classes will be put.
     *
     * @return string
     */
    abstract public function targetPath(): string;

    /**
     * Where to look for .graphql files containing operations.
     *
     * @return string
     */
    abstract public function searchPath(): string;

    /**
     * The location of the schema file that describes the endpoint.
     *
     * @return string
     */
    abstract public function schemaPath(): string;

    public function client(): Client
    {
        if (isset($this->mockClient)) {
            return $this->mockClient;
        }

        return $this->makeClient();
    }
}
