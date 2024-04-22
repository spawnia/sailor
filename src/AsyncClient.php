<?php declare(strict_types=1);

namespace Spawnia\Sailor;


interface AsyncClient extends Client
{
    /** Execute a GraphQL query against an endpoint and return a Response. */
    public function requestAsync(string $query, \stdClass $variables = null): PromiseInterface;
}
