<?php

declare(strict_types=1);

namespace Spawnia\Sailor;

use GraphQL\Type\Introspection;
use GraphQL\Utils\BuildClientSchema;
use GraphQL\Utils\SchemaPrinter;

class Introspector
{
    /**
     * @var EndpointConfig
     */
    protected $endpointConfig;

    public function __construct(EndpointConfig $endpointConfig)
    {
        $this->endpointConfig = $endpointConfig;
    }

    public function introspect(): void
    {
        $client = $this->endpointConfig->client();

        $introspectionResult = $client->request(Introspection::getIntrospectionQuery());
        $introspectionResult->assertErrorFree();

        $schema = BuildClientSchema::build(
            Json::stdClassToAssoc($introspectionResult->data)
        );

        $schemaString = SchemaPrinter::doPrint($schema);

        \Safe\file_put_contents(
            $this->endpointConfig->schemaPath(),
            $schemaString
        );
    }
}
