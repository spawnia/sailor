<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Testing;

use Spawnia\Sailor\Client;
use Spawnia\Sailor\Response;

/**
 * @phpstan-type ResponseMock callable(string, \stdClass|null): Response
 */
class MockClient implements Client
{
    /**
     * @var array<int, ResponseMock>
     */
    public array $responseMocks = [];

    /**
     * @var array<int, MockRequest>
     */
    public array $storedRequests = [];

    public function request(string $query, \stdClass $variables = null): Response
    {
        $this->storedRequests [] = new MockRequest($query, $variables);

        $responseMock = array_shift($this->responseMocks);
        if ($responseMock === null) {
            throw new \Exception('No mock left to handle the request.');
        }

        return $responseMock($query, $variables);
    }
}
