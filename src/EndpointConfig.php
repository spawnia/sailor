<?php

declare(strict_types=1);

namespace Spawnia\Sailor;

abstract class EndpointConfig
{
    /**
     * Instantiate a client that will resolve the GraphQL operations.
     * @param array<string> $options
     */
    abstract public function makeClient(array $options = []): Client;

    /**
     * The namespace the generated classes will be created in.
     */
    abstract public function namespace(): string;

    /**
     * Path to the directory where the generated classes will be put.
     */
    abstract public function targetPath(): string;

    /**
     * Where to look for .graphql files containing operations.
     */
    abstract public function searchPath(): string;

    /**
     * The location of the schema file that describes the endpoint.
     */
    abstract public function schemaPath(): string;


    public function getOperationClass(): string
    {
        return Operation::class;
    }
}
