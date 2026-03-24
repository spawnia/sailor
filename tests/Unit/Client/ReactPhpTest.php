<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit\Client;

use Mockery;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use React\Http\Browser;
use Spawnia\Sailor\Client\ReactPhp;
use Spawnia\Sailor\Error\UnexpectedResponse;
use Spawnia\Sailor\Tests\TestCase;

use function React\Promise\resolve;

/** @requires function React\Async\await */
final class ReactPhpTest extends TestCase
{
    public function testRequest(): void
    {
        $uri = 'https://simple.bar/graphql';
        $expectedBody = /* @lang JSON */ '{"query":"{simple}"}';

        $browser = \Mockery::mock(Browser::class);
        $browser->shouldReceive('post')
            ->once()
            ->withArgs(function (string $url, array $headers, string $body) use ($uri, $expectedBody): bool {
                return $url === $uri
                    && $headers === ['Content-Type' => 'application/json']
                    && $body === $expectedBody;
            })
            ->andReturn(resolve($this->mockResponse(200, /* @lang JSON */ '{"data": {"simple": "bar"}}')));

        $client = new ReactPhp($uri, $browser);
        $response = $client->request(/* @lang GraphQL */ '{simple}');

        self::assertEquals(
            (object) ['simple' => 'bar'],
            $response->data,
        );
    }

    public function testRequestWithVariables(): void
    {
        $uri = 'https://simple.bar/graphql';
        $variables = (object) ['foo' => 'bar'];
        $expectedBody = /* @lang JSON */ '{"query":"{simple}","variables":{"foo":"bar"}}';

        $browser = \Mockery::mock(Browser::class);
        $browser->shouldReceive('post')
            ->once()
            ->withArgs(function (string $url, array $headers, string $body) use ($uri, $expectedBody): bool {
                return $url === $uri
                    && $headers === ['Content-Type' => 'application/json']
                    && $body === $expectedBody;
            })
            ->andReturn(resolve($this->mockResponse(200, /* @lang JSON */ '{"data": {"simple": "bar"}}')));

        $client = new ReactPhp($uri, $browser);
        $response = $client->request(/* @lang GraphQL */ '{simple}', $variables);

        self::assertEquals(
            (object) ['simple' => 'bar'],
            $response->data,
        );
    }

    public function testNon200StatusThrows(): void
    {
        $uri = 'https://simple.bar/graphql';

        $browser = \Mockery::mock(Browser::class);
        $browser->shouldReceive('post')
            ->once()
            ->andReturn(resolve($this->mockResponse(500, 'Internal Server Error')));

        $client = new ReactPhp($uri, $browser);

        $this->expectException(UnexpectedResponse::class);
        $client->request(/* @lang GraphQL */ '{simple}');
    }

    /** @return ResponseInterface&Mockery\MockInterface */
    private function mockResponse(int $statusCode, string $body): ResponseInterface
    {
        $stream = \Mockery::mock(StreamInterface::class);
        $stream->shouldReceive('getContents')->andReturn($body);
        $stream->shouldReceive('__toString')->andReturn($body);

        $response = \Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('getStatusCode')->andReturn($statusCode);
        $response->shouldReceive('getBody')->andReturn($stream);
        $response->shouldReceive('getHeaders')->andReturn([]);

        return $response;
    }
}
