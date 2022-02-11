<?php declare(strict_types=1);

namespace Spawnia\Sailor;

use GraphQL\Type\Schema;

abstract class SchemaConfig
{
    /**
     * Instantiate a client that will resolve the GraphQL operations.
     */
    abstract public function makeClient(): Client;

    /**
     * The location of the schema file that describes the endpoint.
     */
    abstract public function schemaPath(): string;
}
