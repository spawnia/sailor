<?php

declare(strict_types=1);

namespace Spawnia\Sailor;

abstract class EndpointConfig
{
    /**
     * Instantiate a client that will resolve the GraphQL operations.
     */
    abstract public function makeClient(): Client;

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

    /**
     * Map types to a class that specializes its capabilities.
     *
     * @return array<string, TypeConverter|TypeDecorator>
     */
    public function types(): array
    {
        return [];
    }
}
