<?php declare(strict_types=1);

namespace Spawnia\Sailor;

interface Client
{
    /** Execute a GraphQL query against an endpoint and return a Response. */
    public function request(string $query, ?\stdClass $variables = null): Response;
}
