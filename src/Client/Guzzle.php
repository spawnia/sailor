<?php declare(strict_types=1);

namespace Spawnia\Sailor\Client;

use GuzzleHttp\Client as GuzzleClient;
use Spawnia\Sailor\Client;
use Spawnia\Sailor\Response;

class Guzzle implements Client
{
    protected string $uri;

    protected GuzzleClient $guzzle;

    /** @param  array<string, mixed>  $config */
    public function __construct(string $uri, array $config = [])
    {
        $this->uri = $uri;
        $this->guzzle = new GuzzleClient($config);
    }

    public function request(string $query, \stdClass $variables = null): Response
    {
        $json = ['query' => $query];
        if (! is_null($variables)) {
            $json['variables'] = $variables;
        }

        $response = $this->guzzle->post($this->uri, ['json' => $json]);

        return Response::fromResponseInterface($response);
    }
}
