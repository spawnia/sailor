<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Spawnia\Sailor\InvalidResponseException;
use Spawnia\Sailor\Response;
use Spawnia\Sailor\ResultErrorsException;

class ResponseTest extends TestCase
{
    public function testFromResponseInterface(): void
    {
        $stream = self::createMock(StreamInterface::class);
        $stream->method('getContents')
            ->willReturn(/* @lang JSON */ '{"data": {"foo": "bar"}}');

        /** @var MockObject&ResponseInterface $httpResponse */
        $httpResponse = self::createMock(ResponseInterface::class);
        $httpResponse->method('getBody')
            ->willReturn($stream);
        $httpResponse->method('getStatusCode')
            ->willReturn(200);

        $response = Response::fromResponseInterface($httpResponse);

        self::assertResponseIsFooBar($response);
    }

    public function testFromResponseInterfaceNon200(): void
    {
        /** @var MockObject&ResponseInterface $httpResponse */
        $httpResponse = self::createMock(ResponseInterface::class);
        $httpResponse->method('getStatusCode')
            ->willReturn(500);

        self::expectException(InvalidResponseException::class);
        Response::fromResponseInterface($httpResponse);
    }

    public function testFromJson(): void
    {
        $response = Response::fromJson(/* @lang JSON */ '{"data": {"foo": "bar"}}');

        self::assertResponseIsFooBar($response);
    }

    public function testFromInvalidJson(): void
    {
        $invalidJSON = /* @lang JSON */ 'foobar';
        self::expectExceptionMessageMatches("/$invalidJSON/");
        Response::fromJson($invalidJSON);
    }

    public function testValidData(): void
    {
        $response = Response::fromStdClass(
            (object) [
                'data' => (object) [
                    'foo' => 'bar',
                ],
            ]
        );

        self::assertResponseIsFooBar($response);
    }

    public function testInvalidData(): void
    {
        self::expectException(InvalidResponseException::class);
        Response::fromStdClass(
            (object) [
                'data' => [],
            ]
        );
    }

    public function testValidErrors(): void
    {
        $response = Response::fromStdClass(
            (object) [
                'errors' => [
                    (object) [
                        'message' => 'foo',
                    ],
                ],
            ]
        );

        $errors = $response->errors;
        self::assertNotNull($errors);
        self::assertSame('foo', $errors[0]->message);

        self::expectException(ResultErrorsException::class);
        $response->assertErrorFree();
    }

    public function testNotAMap(): void
    {
        $invalidJSON = /* @lang JSON */ '"foobar"';
        self::expectExceptionMessageMatches("/$invalidJSON/");
        Response::fromJson($invalidJSON);
    }

    public function testNoDataAndNoErrors(): void
    {
        self::expectException(InvalidResponseException::class);
        Response::fromStdClass((object) []);
    }

    public function testErrorsAreNotAList(): void
    {
        self::expectException(InvalidResponseException::class);
        Response::fromStdClass((object) ['errors' => 'foobar']);
    }

    public function testErrorsAreEmptyList(): void
    {
        self::expectException(InvalidResponseException::class);
        Response::fromStdClass((object) ['errors' => []]);
    }

    public function testErrorIsNotAMap(): void
    {
        self::expectException(InvalidResponseException::class);
        Response::fromStdClass((object) ['errors' => ['foo']]);
    }

    public function testErrorHasNoMessage(): void
    {
        self::expectException(InvalidResponseException::class);
        Response::fromStdClass((object) [
            'errors' => [
                (object) [
                    'foo' => 'bar',
                ],
            ],
        ]);
    }

    public function testErrorMessageIsNotAString(): void
    {
        self::expectException(InvalidResponseException::class);
        Response::fromStdClass((object) [
            'errors' => [
                (object) [
                    'message' => 123,
                ],
            ],
        ]);
    }

    public function testValidExtensions(): void
    {
        $response = Response::fromStdClass((object) [
            'data' => null,
            'extensions' => (object) [
                'foo' => 123,
            ],
        ]);

        self::assertNull($response->data);
        self::assertSame(123, $response->extensions->foo);
    }

    public function testInvalidExtensions(): void
    {
        self::expectException(InvalidResponseException::class);
        Response::fromStdClass((object) [
            'data' => null,
            'extensions' => 'not a map',
        ]);
    }

    public static function assertResponseIsFooBar(Response $response): void
    {
        self::assertSame('bar', $response->data->foo);
    }
}
