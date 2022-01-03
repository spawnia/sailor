<?php declare(strict_types=1);

namespace Spawnia\Sailor;

use GraphQL\Type\Introspection;
use GraphQL\Utils\BuildClientSchema;
use GraphQL\Utils\SchemaPrinter;
use Spawnia\Sailor\Error\ResultErrorsException;
use stdClass;
use Throwable;

class Introspector
{
    protected EndpointConfig $endpointConfig;

    protected string $endpointName;

    public function __construct(EndpointConfig $endpointConfig, string $endpointName)
    {
        $this->endpointConfig = $endpointConfig;
        $this->endpointName = $endpointName;
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
        $response = $client->request(
            Introspection::getIntrospectionQuery([
                'directiveIsRepeatable' => $directiveIsRepeatable,
            ])
        );

        if (isset($response->errors)) {
            throw new ResultErrorsException(
                array_map(
                    [$this->endpointConfig, 'parseError'],
                    $response->errors
                ),
                $this->endpointName
            );
        }

        return $response;
    }
}
