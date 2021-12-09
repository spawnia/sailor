<?php declare(strict_types=1);

namespace Spawnia\Sailor;

use GraphQL\Type\Introspection;
use GraphQL\Utils\BuildClientSchema;
use GraphQL\Utils\SchemaPrinter;
use Throwable;

class Introspector
{
    protected EndpointConfig $endpointConfig;

    public function __construct(EndpointConfig $endpointConfig)
    {
        $this->endpointConfig = $endpointConfig;
    }

    public function introspect(): void
    {
        $client = $this->endpointConfig->makeClient();

        try {
            $introspectionResult = $this->fetchIntrospectionResult($client, true);
        } catch (Throwable $_) {
            $introspectionResult = $this->fetchIntrospectionResult($client, false);
        }

        $schema = BuildClientSchema::build(
            // @phpstan-ignore-next-line we know a stdClass converts to an associative array
            Json::stdClassToAssoc($introspectionResult->data)
        );

        $schemaString = SchemaPrinter::doPrint($schema);

        \Safe\file_put_contents(
            $this->endpointConfig->schemaPath(),
            $schemaString
        );
    }

    protected function fetchIntrospectionResult(Client $client, bool $directiveIsRepeatable): Response
    {
        return $client
            ->request(
                Introspection::getIntrospectionQuery([
                    'directiveIsRepeatable' => $directiveIsRepeatable,
                ])
            )
            ->assertErrorFree();
    }
}
