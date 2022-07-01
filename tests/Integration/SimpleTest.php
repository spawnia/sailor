<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Integration;

use Mockery;
use Spawnia\Sailor\Client;
use Spawnia\Sailor\Configuration;
use Spawnia\Sailor\EndpointConfig;
use Spawnia\Sailor\Error\ResultErrorsException;
use Spawnia\Sailor\Response;
use Spawnia\Sailor\Simple\Operations\MyObjectNestedQuery;
use Spawnia\Sailor\Simple\Operations\MyObjectNestedQuery\MyObjectNestedQueryResult;
use Spawnia\Sailor\Simple\Operations\MyScalarQuery;
use Spawnia\Sailor\Simple\Operations\MyScalarQuery\MyScalarQueryResult;
use Spawnia\Sailor\Tests\TestCase;

final class SimpleTest extends TestCase
{
    public function testRequest(): void
    {
        $value = 'bar';

        $client = Mockery::mock(Client::class);
        $client->expects('request')
            ->once()
            ->withArgs(function (string $query, \stdClass $variables): bool {
                return $query === MyScalarQuery::document()
                    // @phpstan-ignore-next-line loose comparison
                    && $variables == new \stdClass();
            })
            ->andReturn(Response::fromStdClass((object) [
                'data' => (object) [
                    '__typename' => 'Query',
                    'scalarWithArg' => $value,
                ],
            ]));

        $endpoint = Mockery::mock(EndpointConfig::class);
        $endpoint->expects('makeClient')
            ->once()
            ->withNoArgs()
            ->andReturn($client);

        Configuration::setEndpointFor(MyScalarQuery::class, $endpoint);

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

        Configuration::setEndpointFor(MyScalarQuery::class, $endpoint);

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
        Configuration::setEndpointFor(MyScalarQuery::class, $endpoint);

        MyScalarQuery::setClient($client);
        MyScalarQuery::execute($value);
        MyScalarQuery::setClient(null);
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

        $endpoint = Mockery::mock(EndpointConfig::class)->makePartial();
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
}
