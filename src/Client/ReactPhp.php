<?php declare(strict_types=1);

namespace Spawnia\Sailor\Client;

use React\Http\Browser;
use Spawnia\Sailor\Client;
use Spawnia\Sailor\Response;

use function React\Async\await;
use function Safe\json_encode;

class ReactPhp implements Client
{
    protected string $uri;

    protected Browser $browser;

    public function __construct(string $uri, ?Browser $browser = null)
    {
        $this->uri = $uri;
        $this->browser = $browser ?? new Browser();
    }

    public function request(string $query, ?\stdClass $variables = null): Response
    {
        $body = ['query' => $query];
        if (! is_null($variables)) {
            $body['variables'] = $variables;
        }

        $json = json_encode($body);
        $response = await($this->browser->post($this->uri, ['Content-Type' => 'application/json'], $json));

        return Response::fromResponseInterface($response);
    }
}
