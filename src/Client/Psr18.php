<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Client;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use function Safe\json_encode;
use Spawnia\Sailor\Client;
use Spawnia\Sailor\Response;

class Psr18 implements Client
{
    protected ClientInterface $client;

    protected string $url;

    protected RequestFactoryInterface $requestFactory;

    protected StreamFactoryInterface $streamFactory;

    public function __construct(
        ClientInterface $client,
        string $url = '',
        ?RequestFactoryInterface $requestFactory = null,
        ?StreamFactoryInterface $streamFactory = null
    ) {
        $this->client = $client;
        $this->url = $url;
        $this->requestFactory = $requestFactory ?? new Psr17Factory();
        $this->streamFactory = $streamFactory ?? new Psr17Factory();
    }

    public function request(string $query, \stdClass $variables = null): Response
    {
        $response = $this->client->sendRequest(
            $this->composeRequest($query, $variables)
        );

        return Response::fromResponseInterface($response);
    }

    protected function composeRequest(string $query, ?\stdClass $variables = null): RequestInterface
    {
        $request = $this->requestFactory->createRequest('POST', $this->url);

        $body = ['query' => $query];
        if (! is_null($variables)) {
            $body['variables'] = $variables;
        }
        $bodyStream = $this->streamFactory->createStream(json_encode($body));

        return $request
            ->withHeader('Content-Type', 'application/json')
            ->withBody($bodyStream);
    }
}
