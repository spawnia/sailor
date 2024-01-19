<?php declare(strict_types=1);

namespace Spawnia\Sailor\Testing;

use Spawnia\Sailor\Client;
use Spawnia\Sailor\Response;

/**
 * @phpstan-type Request callable(string, \stdClass|null): Response
 */
class MockClient implements Client
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
}
