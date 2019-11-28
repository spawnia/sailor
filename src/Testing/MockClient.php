<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Testing;

use Spawnia\Sailor\Client;
use Spawnia\Sailor\Response;

class MockClient implements Client
{
    /**
     * @var callable[]
     */
    public $responseMocks = [];

    /**
     * @var MockRequest[]
     */
    public $storedRequests = [];

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
