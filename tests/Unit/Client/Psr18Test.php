<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit\Client;

use GuzzleHttp\Psr7\Response;
use Http\Mock\Client;
use PHPUnit\Framework\TestCase;
use Spawnia\Sailor\Client\Psr18;

final class Psr18Test extends TestCase
{
    public function testRequest(): void
    {
        $mockClient = new Client();
        $mockClient->addResponse(new Response(200, [], /* @lang JSON */ '{"data": {"simple": "bar"}}'));

        $url = 'https://simple.bar/graphql';
        $client = new Psr18($mockClient, $url);
        $response = $client->request(/* @lang GraphQL */ '{simple}', (object) ['key' => 'value']);

        self::assertEquals(
            (object) ['simple' => 'bar'],
            $response->data
        );

        $request = $mockClient->getRequests()[0];

        self::assertSame('POST', $request->getMethod());
        self::assertSame(/* @lang JSON */ '{"query":"{simple}","variables":{"key":"value"}}', $request->getBody()->__toString());
        self::assertSame($url, $request->getUri()->__toString());
    }
}
