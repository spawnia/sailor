<?php

declare(strict_types=1);

namespace Spawnia\Sailor;

class GuzzleClient implements Client
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $guzzle;

    /**
     * @var string
     */
    protected $uri;

    public function __construct(string $uri, array $config = [])
    {
        $this->guzzle = new \GuzzleHttp\Client($config);
        $this->uri = $uri;
    }

    public function request(string $query, \stdClass $variables = null): Response
    {
        $json = ['query' => $query];
        if (! is_null($variables)) {
            $json['variables'] = $variables;
        }

        $response = $this->guzzle->post($this->uri, ['json' => $json]);

        return Response::fromJson(
            $response->getBody()
        );
    }
}
