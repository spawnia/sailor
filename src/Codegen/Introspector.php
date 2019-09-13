<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Type\Introspection;
use Spawnia\Sailor\EndpointConfig;

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

    public function fetch()
    {
        $client = $this->endpointConfig->client();
        $response = $client->request(Introspection::getIntrospectionQuery());

        if ($response->errors) {
            throw new \Exception('Errors while running the introspection query.');
        }

        return $response->data;
    }
}
