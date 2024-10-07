<?php declare(strict_types=1);

namespace Spawnia\Sailor\Events;

use Spawnia\Sailor\Response;

/** Fired after receiving a GraphQL response from the client. */
class ReceiveResponse
{
    public Response $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }
}
