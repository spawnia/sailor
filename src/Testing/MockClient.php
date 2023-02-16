<?php declare(strict_types=1);

namespace Spawnia\Sailor\Testing;

use Spawnia\Sailor\Client;
use Spawnia\Sailor\Response;

/**
 * @phpstan-type ResponseMock callable(string, \stdClass|null): Response
 */
class MockClient implements Client
{
    /**
     * @var ResponseMock
     */
    private $respond;

    /**
     * @var array<int, MockRequest>
     */
    public array $storedRequests = [];

    /**
     * @param ResponseMock $respond
     */
    public function __construct(callable $respond)
    {
        $this->respond = $respond;
    }

    public function request(string $query, \stdClass $variables = null): Response
    {
        $this->storedRequests[] = new MockRequest($query, $variables);

        return ($this->respond)($query, $variables);
    }
}
