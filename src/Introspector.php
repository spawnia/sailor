<?php declare(strict_types=1);

namespace Spawnia\Sailor;

use GraphQL\Type\Introspection;
use GraphQL\Utils\BuildClientSchema;
use GraphQL\Utils\SchemaPrinter;
use Spawnia\Sailor\Error\Error;
use Spawnia\Sailor\Error\ResultErrorsException;
use stdClass;
use Throwable;

class Introspector
{
    protected EndpointConfig $endpointConfig;

    protected string $endpointName;

    protected string $configFile;

    public function __construct(EndpointConfig $endpointConfig, string $configFile, string $endpointName)
    {
        $this->endpointConfig = $endpointConfig;
        $this->configFile = $configFile;
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
                    function (stdClass $raw): Error {
                        $parsed = $this->endpointConfig->parseError($raw);
                        $parsed->configFile = $this->configFile;
                        $parsed->endpointName = $this->endpointName;

                        return $parsed;
                    },
                    $response->errors
                ),
                $this->configFile,
                $this->endpointName
            );
        }

        return $response;
    }
}
