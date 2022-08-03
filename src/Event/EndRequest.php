<?php declare(strict_types=1);

namespace Spawnia\Sailor\Event;

use Spawnia\Sailor\Response;

class EndRequest
{
    public Response $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }
}
