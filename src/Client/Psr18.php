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
use Spawnia\Sailor\InvalidResponseException;
use Spawnia\Sailor\Response;

final class Psr18 implements Client
{
    private ClientInterface $client;

    private string $url;

    private RequestFactoryInterface $requestFactory;

    private StreamFactoryInterface $streamFactory;

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

        if ($response->getStatusCode() !== 200) {
            throw new InvalidResponseException('Response must have status code 200, got '.$response->getStatusCode());
        }

        return Response::fromResponseInterface($response);
    }

    private function composeRequest(string $query, ?\stdClass $variables = null): RequestInterface
    {
        $request = $this->requestFactory->createRequest('POST', $this->url);

        $body = ['query' => $query];
        if ($variables !== null) {
            /** @var array<string, mixed> $variablesArray */
            $variablesArray = (array) $variables;
            $body['variables'] = $variablesArray;
        }

        return $request
            ->withHeader('Content-Type', 'application/json')
            ->withBody(
                $this->streamFactory->createStream(json_encode($body))
            );
    }
}
