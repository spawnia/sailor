<?php

declare(strict_types=1);

namespace Spawnia\Sailor;

interface Client
{
    /**
     * Execute a GraphQL query against an endpoint and return a Response.
     *
     * @param  string  $query
     * @param  \stdClass|null  $variables
     * @return Response
     */
    public function request(string $query, \stdClass $variables = null): Response;
}
