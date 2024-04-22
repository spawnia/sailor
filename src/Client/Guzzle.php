<?php declare(strict_types=1);

namespace Spawnia\Sailor\Client;

use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Message\ResponseInterface;
use Spawnia\Sailor\AsyncClient;
use Spawnia\Sailor\PromiseInterface;
use Spawnia\Sailor\Response;

class Guzzle implements AsyncClient
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

    public function requestAsync(string $query, \stdClass $variables = null): PromiseInterface
    {
        $json = ['query' => $query];
        if (! is_null($variables)) {
            $json['variables'] = $variables;
        }

        $promise = $this->guzzle->postAsync($this->uri, ['json' => $json]);

        return new GuzzlePromiseAdapter($promise->then(
            function (ResponseInterface $response) {
                return Response::fromResponseInterface($response);
            }
        ));
    }
}
