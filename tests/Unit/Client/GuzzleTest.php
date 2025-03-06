<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit\Client;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Spawnia\Sailor\Client\Guzzle;
use Spawnia\Sailor\Tests\TestCase;

final class GuzzleTest extends TestCase
{
    public function testRequest(): void
    {
        $container = [];
        $history = Middleware::history($container);

        $mock = new MockHandler([
            new Response(200, [], /* @lang JSON */ '{"data": {"simple": "bar"}}'),
        ]);
        $stack = HandlerStack::create($mock);
        $stack->push($history);

        $uri = 'https://simple.bar/graphql';
        $client = new Guzzle($uri, ['handler' => $stack]);
        $response = $client->request(/* @lang GraphQL */ '{simple}');

        self::assertEquals(
            (object) ['simple' => 'bar'],
            $response->data
        );

        $firstHistoryEntry = $container[0];
        assert(is_array($firstHistoryEntry));

        $request = $firstHistoryEntry['request'];
        assert($request instanceof Request);

        self::assertSame('POST', $request->getMethod());
        self::assertSame(/* @lang JSON */ '{"query":"{simple}"}', $request->getBody()->getContents());
        self::assertSame($uri, $request->getUri()->__toString());
    }
}
