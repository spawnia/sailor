<?php declare(strict_types=1);

namespace Spawnia\Sailor\Testing;

use GuzzleHttp\Promise\Promise;
use Spawnia\Sailor\AsyncClient;
use Spawnia\Sailor\Client;
use Spawnia\Sailor\Response;

/**
 * @phpstan-type Request callable(string, \stdClass|null): Response
 */
class MockClient implements AsyncClient
{
    /** @var Request */
    private $request;

    /** @var array<int, MockRequest> */
    public array $storedRequests = [];

    /** @param Request $request */
    public function __construct(callable $request)
    {
        $this->request = $request;
    }

    public function request(string $query, \stdClass $variables = null): Response
    {
        $this->storedRequests[] = new MockRequest($query, $variables);

        return ($this->request)($query, $variables);
    }

    public function requestAsync(string $query, ?\stdClass $variables = null): \Spawnia\Sailor\PromiseInterface
    {
        $this->storedRequests[] = new MockRequest($query, $variables);
        $promise = new Promise(function () use (&$promise, $query, $variables) {
            $response = ($this->request)($query, $variables);
            /** @var Promise $promise */
            $promise->resolve($response);
        });

        return new Client\GuzzlePromiseAdapter($promise);
    }
}
