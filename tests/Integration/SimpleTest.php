<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Integration;

use Spawnia\Sailor\Client;
use Spawnia\Sailor\Configuration;
use Spawnia\Sailor\EndpointConfig;
use Spawnia\Sailor\Error\ResultErrorsException;
use Spawnia\Sailor\Events\ReceiveResponse;
use Spawnia\Sailor\Events\StartRequest;
use Spawnia\Sailor\Response;
use Spawnia\Sailor\Simple\Operations\ClientDirectiveFragmentSpreadQuery;
use Spawnia\Sailor\Simple\Operations\ClientDirectiveInlineFragmentQuery;
use Spawnia\Sailor\Simple\Operations\ClientDirectiveQuery;
use Spawnia\Sailor\Simple\Operations\IncludeNonNullable;
use Spawnia\Sailor\Simple\Operations\MyObjectNestedQuery;
use Spawnia\Sailor\Simple\Operations\MyScalarQuery;
use Spawnia\Sailor\Simple\Operations\SkipNonNullable;
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

        $result = MyScalarQuery::execute($value);
        self::assertNull($result->data);
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
            ->andReturn(MyScalarQuery\MyScalarQueryResult::fromData(
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
        Configuration::setEndpointFor(MyScalarQuery\MyScalarQueryResult::class, $endpoint);

        MyScalarQuery::mock()
            ->expects('execute')
            ->andReturn(MyScalarQuery\MyScalarQueryResult::fromStdClass((object) [
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
            ->andReturn(MyObjectNestedQuery\MyObjectNestedQueryResult::fromData(
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
            ->andReturn(MyObjectNestedQuery\MyObjectNestedQueryResult::fromData(
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

    /** Server omits non-nullable field due to @skip directive. */
    public function testSkipNonNullableFieldOmitted(): void
    {
        $result = SkipNonNullable\SkipNonNullable::fromStdClass((object) [
            '__typename' => 'Query',
        ]);

        self::assertNull($result->nonNullable);
    }

    /** Server returns non-nullable field despite @skip directive (skip condition was false). */
    public function testSkipNonNullableFieldPresent(): void
    {
        $result = SkipNonNullable\SkipNonNullable::fromStdClass((object) [
            '__typename' => 'Query',
            'nonNullable' => 'value',
        ]);

        self::assertSame('value', $result->nonNullable);
    }

    /** Server omits non-nullable field due to @include directive. */
    public function testIncludeNonNullableFieldOmitted(): void
    {
        $result = IncludeNonNullable\IncludeNonNullable::fromStdClass((object) [
            '__typename' => 'Query',
        ]);

        self::assertNull($result->nonNullable);
    }

    /** Server returns non-nullable field because @include condition was true. */
    public function testIncludeNonNullableFieldPresent(): void
    {
        $result = IncludeNonNullable\IncludeNonNullable::fromStdClass((object) [
            '__typename' => 'Query',
            'nonNullable' => 'value',
        ]);

        self::assertSame('value', $result->nonNullable);
    }

    /** @skip on nullable field — field omitted from response. */
    public function testSkipNullableFieldOmitted(): void
    {
        $result = ClientDirectiveQuery\ClientDirectiveQuery::fromStdClass((object) [
            '__typename' => 'Query',
            'twoArgs' => 'present',
        ]);

        self::assertNull($result->scalarWithArg);
        self::assertSame('present', $result->twoArgs);
    }

    /** @include on nullable field — field omitted from response. */
    public function testIncludeNullableFieldOmitted(): void
    {
        $result = ClientDirectiveQuery\ClientDirectiveQuery::fromStdClass((object) [
            '__typename' => 'Query',
            'scalarWithArg' => 'present',
        ]);

        self::assertNull($result->twoArgs);
        self::assertSame('present', $result->scalarWithArg);
    }

    /** All directive-affected fields omitted simultaneously. */
    public function testClientDirectiveAllFieldsOmitted(): void
    {
        $result = ClientDirectiveQuery\ClientDirectiveQuery::fromStdClass((object) [
            '__typename' => 'Query',
        ]);

        self::assertNull($result->scalarWithArg);
        self::assertNull($result->twoArgs);
    }

    /** All fields present despite directives (conditions evaluated to keep fields). */
    public function testClientDirectiveAllFieldsPresent(): void
    {
        $result = ClientDirectiveQuery\ClientDirectiveQuery::fromStdClass((object) [
            '__typename' => 'Query',
            'scalarWithArg' => 'foo',
            'twoArgs' => 'bar',
        ]);

        self::assertSame('foo', $result->scalarWithArg);
        self::assertSame('bar', $result->twoArgs);
    }

    /** Fragment spread with @skip — fields from the fragment are omitted. */
    public function testFragmentSpreadSkipOmitsField(): void
    {
        $result = ClientDirectiveFragmentSpreadQuery\ClientDirectiveFragmentSpreadQuery::fromStdClass((object) [
            '__typename' => 'Query',
        ]);

        self::assertNull($result->twoArgs);
    }

    /** Fragment spread with @skip — field present when skip condition is false. */
    public function testFragmentSpreadSkipFieldPresent(): void
    {
        $result = ClientDirectiveFragmentSpreadQuery\ClientDirectiveFragmentSpreadQuery::fromStdClass((object) [
            '__typename' => 'Query',
            'twoArgs' => 'value',
        ]);

        self::assertSame('value', $result->twoArgs);
    }

    /** Inline fragment with @skip — fields from the fragment are omitted. */
    public function testInlineFragmentSkipOmitsField(): void
    {
        $result = ClientDirectiveInlineFragmentQuery\ClientDirectiveInlineFragmentQuery::fromStdClass((object) [
            '__typename' => 'Query',
        ]);

        self::assertNull($result->twoArgs);
    }

    /** Inline fragment with @skip — field present when skip condition is false. */
    public function testInlineFragmentSkipFieldPresent(): void
    {
        $result = ClientDirectiveInlineFragmentQuery\ClientDirectiveInlineFragmentQuery::fromStdClass((object) [
            '__typename' => 'Query',
            'twoArgs' => 'value',
        ]);

        self::assertSame('value', $result->twoArgs);
    }

    /** Via Result::fromStdClass with the full response envelope — field omitted. */
    public function testSkipNonNullableViaResult(): void
    {
        $result = SkipNonNullable\SkipNonNullableResult::fromStdClass((object) [
            'data' => (object) [
                '__typename' => 'Query',
            ],
        ]);

        self::assertNotNull($result->data);
        self::assertNull($result->data->nonNullable);
    }

    /** Via Result::fromStdClass with the full response envelope — field present. */
    public function testSkipNonNullableViaResultFieldPresent(): void
    {
        $result = SkipNonNullable\SkipNonNullableResult::fromStdClass((object) [
            'data' => (object) [
                '__typename' => 'Query',
                'nonNullable' => 'hello',
            ],
        ]);

        self::assertNotNull($result->data);
        self::assertSame('hello', $result->data->nonNullable);
    }
}
