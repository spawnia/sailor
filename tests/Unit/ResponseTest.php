<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit;

use Spawnia\Sailor\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ResponseInterface;

class ResponseTest extends TestCase
{
    public function testFromResponseInterface(): void
    {
        $stream = self::createMock(StreamInterface::class);
        $stream->method('getContents')
            ->willReturn(/* @lang JSON */ '{"data": {"foo": true}}');

        $httpResponse = self::createMock(ResponseInterface::class);
        $httpResponse->method('getBody')
            ->willReturn($stream);

        $response = Response::fromResponseInterface($httpResponse);

        $data = new \stdClass();
        $data->foo = true;
        $this->assertEquals($data, $response->data);
    }

    public function testFromJson(): void
    {
        $response = Response::fromJson(/* @lang JSON */ '{"data": {"foo": true}}');

        $data = new \stdClass();
        $data->foo = true;
        $this->assertEquals($data, $response->data);
    }

    public function testFromStdClass(): void
    {
        $data = new \stdClass();
        $data->foo = true;

        $payload = new \stdClass();
        $payload->data = $data;

        $response = Response::fromStdClass($payload);
        $this->assertEquals($data, $response->data);
    }
}
