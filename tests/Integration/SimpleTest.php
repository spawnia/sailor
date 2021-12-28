<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Integration;

use Mockery;
use Spawnia\Sailor\Client;
use Spawnia\Sailor\Configuration;
use Spawnia\Sailor\EndpointConfig;
use Spawnia\Sailor\Response;
use Spawnia\Sailor\ResultErrorsException;
use Spawnia\Sailor\Simple\Operations\MyObjectNestedQuery;
use Spawnia\Sailor\Simple\Operations\MyObjectNestedQuery\MyObjectNestedQueryResult;
use Spawnia\Sailor\Simple\Operations\MyScalarQuery;
use Spawnia\Sailor\Simple\Operations\MyScalarQuery\MyScalarQueryResult;
use Spawnia\Sailor\Tests\TestCase;

class SimpleTest extends TestCase
{
    public function testRequest(): void
    {
        $value = 'bar';

        $client = Mockery::mock(Client::class);
        $client->expects('request')
            ->once()
            ->withArgs(function (string $query, \stdClass $variables): bool {
                return $query === MyScalarQuery::document()
                    && $variables == new \stdClass();
            })
            ->andReturn(Response::fromStdClass((object) [
                'data' => (object) [
                    'scalarWithArg' => $value,
                ],
            ]));

        $endpoint = Mockery::mock(EndpointConfig::class);
        $endpoint->expects('makeClient')
            ->once()
            ->withNoArgs()
            ->andReturn($client);

        Configuration::setEndpoint(MyScalarQuery::endpoint(), $endpoint);

        $result = MyScalarQuery::execute()->errorFree();
        self::assertSame($value, $result->data->scalarWithArg);
    }

    public function testRequestWithVariable(): void
    {
        $value = 'bar';

        $client = Mockery::mock(Client::class);
        $client->expects('request')
            ->once()
            ->withArgs(function (string $query, \stdClass $variables) use ($value): bool {
                return $query === MyScalarQuery::document()
                    && $variables->arg === $value;
            })
            ->andReturn(Response::fromStdClass((object) [
                'data' => null,
            ]));

        $endpoint = Mockery::mock(EndpointConfig::class);
        $endpoint->expects('makeClient')
            ->once()
            ->withNoArgs()
            ->andReturn($client);

        Configuration::setEndpoint(MyScalarQuery::endpoint(), $endpoint);

        $result = MyScalarQuery::execute($value);
        self::assertNull($result->data);
    }

    public function testRequestWithClient(): void
    {
        $value = 'bar';

        $client = Mockery::mock(Client::class);
        $client->expects('request')
            ->once()
            ->withArgs(function (string $query, \stdClass $variables) use ($value): bool {
                return $query === MyScalarQuery::document()
                    && $variables->arg === $value;
            });

        $endpoint = Mockery::mock(EndpointConfig::class);
        $endpoint->expects('makeClient')
            ->never();
        Configuration::setEndpoint(MyScalarQuery::endpoint(), $endpoint);

        MyScalarQuery::setClient($client);
        MyScalarQuery::execute($value);
        MyScalarQuery::setClient(null);
    }

    public function testMockResult(): void
    {
        $bar = 'bar';

        MyScalarQuery::mock()
            ->expects('execute')
            ->andReturn(MyScalarQueryResult::fromStdClass((object) [
                'data' => (object) [
                    'scalarWithArg' => $bar,
                ],
            ]));

        self::assertSame($bar, MyScalarQuery::execute()->errorFree()->data->scalarWithArg);
    }

    public function testMockError(): void
    {
        $message = 'some error';

        $endpoint = Mockery::mock(EndpointConfig::class)->makePartial();
        Configuration::setEndpoint(MyScalarQueryResult::endpoint(), $endpoint);

        MyScalarQuery::mock()
            ->expects('execute')
            ->andReturn(MyScalarQueryResult::fromStdClass((object) [
                'data' => null,
                'errors' => [
                    (object) [
                        'message' => $message,
                    ],
                ],
            ]));

        $result = MyScalarQuery::execute();
        $errors = $result->errors;
        self::assertNotNull($errors);
        self::assertSame('some error', $errors[0]->message);

        self::expectException(ResultErrorsException::class);
        $result->errorFree();
    }

    public function testNestedObject(): void
    {
        $value = 42;

        MyObjectNestedQuery::mock()
            ->expects('execute')
            ->once()
            ->with()
            ->andReturn(MyObjectNestedQueryResult::fromStdClass((object) [
                'data' => (object) [
                    'singleObject' => (object) [
                        'nested' => (object) [
                            'value' => $value,
                        ],
                    ],
                ],
            ]));

        $result = MyObjectNestedQuery::execute()->errorFree();
        $object = $result->data->singleObject;
        self::assertNotNull($object);

        $nested = $object->nested;
        self::assertNotNull($nested);
        self::assertSame($value, $nested->value);
    }

    public function testNestedObjectNull(): void
    {
        MyObjectNestedQuery::mock()
            ->expects('execute')
            ->once()
            ->with()
            ->andReturn(MyObjectNestedQueryResult::fromStdClass((object) [
                'data' => (object) [
                    'singleObject' => (object) [
                        'nested' => null,
                    ],
                ],
            ]));

        $result = MyObjectNestedQuery::execute()->errorFree();
        $object = $result->data->singleObject;
        self::assertNotNull($object);
        self::assertNull($object->nested);
    }
}
