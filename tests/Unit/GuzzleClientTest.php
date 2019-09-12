<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit;

use GuzzleHttp\Middleware;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Spawnia\Sailor\GuzzleClient;
use GuzzleHttp\Handler\MockHandler;

class GuzzleClientTest extends TestCase
{
    public function testRequest(): void
    {
        $container = [];
        $history = Middleware::history($container);

        $mock = new MockHandler([
            new Response(200, [], /* @lang JSON */ '{"data": {"foo": "bar"}}'),
        ]);
        $stack = HandlerStack::create($mock);
        $stack->push($history);

        $client = new GuzzleClient('http://foo.bar/graphql', ['handler' => $stack]);
        $response = $client->request(/* @lang GraphQL */ '{foo}');

        $this->assertEquals(
            (object) ['foo' => 'bar'],
            $response->data
        );

        /** @var Request $request */
        $request = $container[0]['request'];

        $this->assertSame('POST', $request->getMethod());
        $this->assertSame(/* @lang JSON */ '{"query":"{foo}"}', $request->getBody()->getContents());
    }
}
