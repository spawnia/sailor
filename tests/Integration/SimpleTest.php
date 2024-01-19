<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Integration;

use Spawnia\Sailor\Client;
use Spawnia\Sailor\Configuration;
use Spawnia\Sailor\EndpointConfig;
use Spawnia\Sailor\Error\ResultErrorsException;
use Spawnia\Sailor\Events\ReceiveResponse;
use Spawnia\Sailor\Events\StartRequest;
use Spawnia\Sailor\Response;
use Spawnia\Sailor\Simple\Operations\MyObjectNestedQuery;
use Spawnia\Sailor\Simple\Operations\MyObjectNestedQuery\MyObjectNestedQueryResult;
use Spawnia\Sailor\Simple\Operations\MyScalarQuery;
use Spawnia\Sailor\Simple\Operations\MyScalarQuery\MyScalarQueryResult;
use Spawnia\Sailor\Simple\Operations\SkipNonNullable\SkipNonNullable;
use Spawnia\Sailor\Tests\TestCase;

final class SimpleTest extends TestCase
{
    public function testRequest(): void
    {
        $value = 'bar';
        $response = Response::fromStdClass((object) [
            'data' => (object) [
                '__typename' => 'Query',
                'scalarWithArg' => $value,
            ],
        ]);

        $client = \Mockery::mock(Client::class);
        $client->expects('request')
            ->once()
            ->withArgs(fn (string $query, \stdClass $variables): bool => $query === MyScalarQuery::document()
                // @phpstan-ignore-next-line loose comparison
                && $variables == new \stdClass())
            ->andReturn($response);

        $endpoint = \Mockery::mock(EndpointConfig::class);
        $endpoint->expects('makeClient')
            ->once()
            ->withNoArgs()
            ->andReturn($client);
        $endpoint->expects('handleEvent')
            ->once()
            ->withArgs(fn (StartRequest $event): bool => $event->document === MyScalarQuery::document()
                // @phpstan-ignore-next-line loose comparison
                && $event->variables == new \stdClass());
        $endpoint->expects('handleEvent')
            ->once()
            ->withArgs(fn (ReceiveResponse $event): bool => $event->response === $response);

        Configuration::setEndpointFor(MyScalarQuery::class, $endpoint);

        self::assertSame($value, MyScalarQuery::execute()->errorFree()->data->scalarWithArg);
    }

    public function testRequestWithVariable(): void
    {
        $value = 'bar';
        $response = Response::fromStdClass((object) [
            'data' => null,
        ]);

        $client = \Mockery::mock(Client::class);
        $client->expects('request')
            ->once()
            ->withArgs(fn (string $query, \stdClass $variables): bool => $query === MyScalarQuery::document()
                && $variables->arg === $value)
            ->andReturn($response);

        $endpoint = \Mockery::mock(EndpointConfig::class);
        $endpoint->expects('makeClient')
            ->once()
            ->withNoArgs()
            ->andReturn($client);
        $endpoint->expects('handleEvent')
            ->once()
            ->withArgs(fn (StartRequest $event): bool => $event->document === MyScalarQuery::document()
                && $event->variables->arg === $value);
        $endpoint->expects('handleEvent')
            ->once()
            ->withArgs(fn (ReceiveResponse $event): bool => $event->response === $response);

        Configuration::setEndpointFor(MyScalarQuery::class, $endpoint);

        self::assertNull(MyScalarQuery::execute($value)->data);
    }

    public function testRequestWithClient(): void
    {
        $value = 'bar';
        $response = Response::fromStdClass((object) [
            'data' => null,
        ]);

        $client = \Mockery::mock(Client::class);
        $client->expects('request')
            ->once()
            ->withArgs(fn (string $query, \stdClass $variables): bool => $query === MyScalarQuery::document()
                && $variables->arg === $value)
            ->andReturn($response);

        $endpoint = \Mockery::mock(EndpointConfig::class);
        $endpoint->expects('makeClient')
            ->never();
        $endpoint->expects('handleEvent')
            ->twice();

        Configuration::setEndpointFor(MyScalarQuery::class, $endpoint);

        MyScalarQuery::setClient($client);
        MyScalarQuery::execute($value);
    }

    public function testMockResult(): void
    {
        $bar = 'bar';

        MyScalarQuery::mock()
            ->expects('execute')
            ->andReturn(MyScalarQueryResult::fromData(
                MyScalarQuery\MyScalarQuery::make(
                    /* scalarWithArg: */
                    $bar
                )
            ));

        self::assertSame($bar, MyScalarQuery::execute()->errorFree()->data->scalarWithArg);
    }

    public function testMockError(): void
    {
        $message = 'some error';

        $endpoint = \Mockery::mock(EndpointConfig::class)->makePartial();
        Configuration::setEndpointFor(MyScalarQueryResult::class, $endpoint);

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
            ->andReturn(MyObjectNestedQueryResult::fromData(
                MyObjectNestedQuery\MyObjectNestedQuery::make(
                    /* singleObject: */
                    MyObjectNestedQuery\SingleObject\SomeObject::make(
                        /* nested: */
                        MyObjectNestedQuery\SingleObject\Nested\SomeObject::make(
                            /* value: */
                            $value
                        )
                    )
                )
            ));

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
            ->andReturn(MyObjectNestedQueryResult::fromData(
                MyObjectNestedQuery\MyObjectNestedQuery::make(
                    /* singleObject: */
                    MyObjectNestedQuery\SingleObject\SomeObject::make(
                        /* nested: */
                        null
                    )
                )
            ));

        $result = MyObjectNestedQuery::execute()->errorFree();
        $object = $result->data->singleObject;
        self::assertNotNull($object);
        self::assertNull($object->nested);
    }

    public function testSkipNonNullable(): void
    {
        SkipNonNullable::fromStdClass((object) [
            'data' => (object) [
                '__typename' => 'Query',
            ],
        ]);
    }
}
