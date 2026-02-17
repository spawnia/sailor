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

    /** Via Result::fromStdClass with the full response envelope for IncludeNonNullable — field omitted. */
    public function testIncludeNonNullableViaResult(): void
    {
        $result = IncludeNonNullable\IncludeNonNullableResult::fromStdClass((object) [
            'data' => (object) [
                '__typename' => 'Query',
            ],
        ]);

        self::assertNotNull($result->data);
        self::assertNull($result->data->nonNullable);
    }

    /** Via Result::fromStdClass with the full response envelope for IncludeNonNullable — field present. */
    public function testIncludeNonNullableViaResultFieldPresent(): void
    {
        $result = IncludeNonNullable\IncludeNonNullableResult::fromStdClass((object) [
            'data' => (object) [
                '__typename' => 'Query',
                'nonNullable' => 'world',
            ],
        ]);

        self::assertNotNull($result->data);
        self::assertSame('world', $result->data->nonNullable);
    }

    /** Test SkipNonNullable operation execution with mocked client when skip is true. */
    public function testSkipNonNullableExecuteWhenSkipTrue(): void
    {
        $value = true;
        $response = Response::fromStdClass((object) [
            'data' => (object) [
                '__typename' => 'Query',
            ],
        ]);

        $client = \Mockery::mock(Client::class);
        $client->expects('request')
            ->once()
            ->withArgs(fn (string $query, \stdClass $variables): bool => $query === SkipNonNullable::document()
                && $variables->value === $value)
            ->andReturn($response);

        $endpoint = \Mockery::mock(EndpointConfig::class);
        $endpoint->expects('makeClient')
            ->once()
            ->withNoArgs()
            ->andReturn($client);
        $endpoint->expects('handleEvent')
            ->twice();

        Configuration::setEndpointFor(SkipNonNullable::class, $endpoint);

        $result = SkipNonNullable::execute($value);
        self::assertNotNull($result->data);
        self::assertNull($result->data->nonNullable);
    }

    /** Test SkipNonNullable operation execution with mocked client when skip is false. */
    public function testSkipNonNullableExecuteWhenSkipFalse(): void
    {
        $value = false;
        $response = Response::fromStdClass((object) [
            'data' => (object) [
                '__typename' => 'Query',
                'nonNullable' => 'value returned',
            ],
        ]);

        $client = \Mockery::mock(Client::class);
        $client->expects('request')
            ->once()
            ->withArgs(fn (string $query, \stdClass $variables): bool => $query === SkipNonNullable::document()
                && $variables->value === $value)
            ->andReturn($response);

        $endpoint = \Mockery::mock(EndpointConfig::class);
        $endpoint->expects('makeClient')
            ->once()
            ->withNoArgs()
            ->andReturn($client);
        $endpoint->expects('handleEvent')
            ->twice();

        Configuration::setEndpointFor(SkipNonNullable::class, $endpoint);

        $result = SkipNonNullable::execute($value);
        self::assertNotNull($result->data);
        self::assertSame('value returned', $result->data->nonNullable);
    }

    /** Test IncludeNonNullable operation execution with mocked client when include is false. */
    public function testIncludeNonNullableExecuteWhenIncludeFalse(): void
    {
        $value = false;
        $response = Response::fromStdClass((object) [
            'data' => (object) [
                '__typename' => 'Query',
            ],
        ]);

        $client = \Mockery::mock(Client::class);
        $client->expects('request')
            ->once()
            ->withArgs(fn (string $query, \stdClass $variables): bool => $query === IncludeNonNullable::document()
                && $variables->value === $value)
            ->andReturn($response);

        $endpoint = \Mockery::mock(EndpointConfig::class);
        $endpoint->expects('makeClient')
            ->once()
            ->withNoArgs()
            ->andReturn($client);
        $endpoint->expects('handleEvent')
            ->twice();

        Configuration::setEndpointFor(IncludeNonNullable::class, $endpoint);

        $result = IncludeNonNullable::execute($value);
        self::assertNotNull($result->data);
        self::assertNull($result->data->nonNullable);
    }

    /** Test IncludeNonNullable operation execution with mocked client when include is true. */
    public function testIncludeNonNullableExecuteWhenIncludeTrue(): void
    {
        $value = true;
        $response = Response::fromStdClass((object) [
            'data' => (object) [
                '__typename' => 'Query',
                'nonNullable' => 'included value',
            ],
        ]);

        $client = \Mockery::mock(Client::class);
        $client->expects('request')
            ->once()
            ->withArgs(fn (string $query, \stdClass $variables): bool => $query === IncludeNonNullable::document()
                && $variables->value === $value)
            ->andReturn($response);

        $endpoint = \Mockery::mock(EndpointConfig::class);
        $endpoint->expects('makeClient')
            ->once()
            ->withNoArgs()
            ->andReturn($client);
        $endpoint->expects('handleEvent')
            ->twice();

        Configuration::setEndpointFor(IncludeNonNullable::class, $endpoint);

        $result = IncludeNonNullable::execute($value);
        self::assertNotNull($result->data);
        self::assertSame('included value', $result->data->nonNullable);
    }

    /** Test SkipNonNullable Result::fromData method. */
    public function testSkipNonNullableResultFromData(): void
    {
        $data = SkipNonNullable\SkipNonNullable::make('test value');
        $result = SkipNonNullable\SkipNonNullableResult::fromData($data);

        self::assertNotNull($result->data);
        self::assertSame('test value', $result->data->nonNullable);
        self::assertSame('Query', $result->data->__typename);
    }

    /** Test IncludeNonNullable Result::fromData method. */
    public function testIncludeNonNullableResultFromData(): void
    {
        $data = IncludeNonNullable\IncludeNonNullable::make('another value');
        $result = IncludeNonNullable\IncludeNonNullableResult::fromData($data);

        self::assertNotNull($result->data);
        self::assertSame('another value', $result->data->nonNullable);
        self::assertSame('Query', $result->data->__typename);
    }

    /** Test SkipNonNullable Result::errorFree when there are no errors. */
    public function testSkipNonNullableErrorFree(): void
    {
        $result = SkipNonNullable\SkipNonNullableResult::fromStdClass((object) [
            'data' => (object) [
                '__typename' => 'Query',
                'nonNullable' => 'error free value',
            ],
        ]);

        $errorFreeResult = $result->errorFree();
        self::assertInstanceOf(SkipNonNullable\SkipNonNullableErrorFreeResult::class, $errorFreeResult);
        self::assertSame('error free value', $errorFreeResult->data->nonNullable);
    }

    /** Test IncludeNonNullable Result::errorFree when there are no errors. */
    public function testIncludeNonNullableErrorFree(): void
    {
        $result = IncludeNonNullable\IncludeNonNullableResult::fromStdClass((object) [
            'data' => (object) [
                '__typename' => 'Query',
                'nonNullable' => 'no errors here',
            ],
        ]);

        $errorFreeResult = $result->errorFree();
        self::assertInstanceOf(IncludeNonNullable\IncludeNonNullableErrorFreeResult::class, $errorFreeResult);
        self::assertSame('no errors here', $errorFreeResult->data->nonNullable);
    }

    /** Test SkipNonNullable Result::errorFree throws exception when there are errors. */
    public function testSkipNonNullableErrorFreeThrowsWithErrors(): void
    {
        $endpoint = \Mockery::mock(EndpointConfig::class)->makePartial();
        Configuration::setEndpointFor(SkipNonNullable\SkipNonNullableResult::class, $endpoint);

        $result = SkipNonNullable\SkipNonNullableResult::fromStdClass((object) [
            'data' => null,
            'errors' => [
                (object) [
                    'message' => 'Something went wrong',
                ],
            ],
        ]);

        self::expectException(ResultErrorsException::class);
        $result->errorFree();
    }

    /** Test IncludeNonNullable Result::errorFree throws exception when there are errors. */
    public function testIncludeNonNullableErrorFreeThrowsWithErrors(): void
    {
        $endpoint = \Mockery::mock(EndpointConfig::class)->makePartial();
        Configuration::setEndpointFor(IncludeNonNullable\IncludeNonNullableResult::class, $endpoint);

        $result = IncludeNonNullable\IncludeNonNullableResult::fromStdClass((object) [
            'data' => null,
            'errors' => [
                (object) [
                    'message' => 'Failed to fetch',
                ],
            ],
        ]);

        self::expectException(ResultErrorsException::class);
        $result->errorFree();
    }

    /** Test SkipNonNullable with errors in response. */
    public function testSkipNonNullableWithErrors(): void
    {
        $endpoint = \Mockery::mock(EndpointConfig::class)->makePartial();
        Configuration::setEndpointFor(SkipNonNullable\SkipNonNullableResult::class, $endpoint);

        $errorMessage = 'Field error occurred';
        $result = SkipNonNullable\SkipNonNullableResult::fromStdClass((object) [
            'data' => (object) [
                '__typename' => 'Query',
            ],
            'errors' => [
                (object) [
                    'message' => $errorMessage,
                    'path' => ['nonNullable'],
                ],
            ],
        ]);

        $errors = $result->errors;
        self::assertNotNull($errors);
        self::assertCount(1, $errors);
        self::assertSame($errorMessage, $errors[0]->message);
    }

    /** Test IncludeNonNullable with errors in response. */
    public function testIncludeNonNullableWithErrors(): void
    {
        $endpoint = \Mockery::mock(EndpointConfig::class)->makePartial();
        Configuration::setEndpointFor(IncludeNonNullable\IncludeNonNullableResult::class, $endpoint);

        $errorMessage = 'Authorization failed';
        $result = IncludeNonNullable\IncludeNonNullableResult::fromStdClass((object) [
            'data' => null,
            'errors' => [
                (object) [
                    'message' => $errorMessage,
                ],
            ],
        ]);

        $errors = $result->errors;
        self::assertNotNull($errors);
        self::assertCount(1, $errors);
        self::assertSame($errorMessage, $errors[0]->message);
        self::assertNull($result->data);
    }

    /** Test SkipNonNullable operation document method returns correct GraphQL query. */
    public function testSkipNonNullableDocument(): void
    {
        $document = SkipNonNullable::document();
        self::assertStringContainsString('query SkipNonNullable', $document);
        self::assertStringContainsString('$value: Boolean!', $document);
        self::assertStringContainsString('nonNullable @skip(if: $value)', $document);
        self::assertStringContainsString('__typename', $document);
    }

    /** Test IncludeNonNullable operation document method returns correct GraphQL query. */
    public function testIncludeNonNullableDocument(): void
    {
        $document = IncludeNonNullable::document();
        self::assertStringContainsString('query IncludeNonNullable', $document);
        self::assertStringContainsString('$value: Boolean!', $document);
        self::assertStringContainsString('nonNullable @include(if: $value)', $document);
        self::assertStringContainsString('__typename', $document);
    }

    /** Test SkipNonNullable operation endpoint method. */
    public function testSkipNonNullableEndpoint(): void
    {
        self::assertSame('simple', SkipNonNullable::endpoint());
        self::assertSame('simple', SkipNonNullable\SkipNonNullable::endpoint());
        self::assertSame('simple', SkipNonNullable\SkipNonNullableResult::endpoint());
        self::assertSame('simple', SkipNonNullable\SkipNonNullableErrorFreeResult::endpoint());
    }

    /** Test IncludeNonNullable operation endpoint method. */
    public function testIncludeNonNullableEndpoint(): void
    {
        self::assertSame('simple', IncludeNonNullable::endpoint());
        self::assertSame('simple', IncludeNonNullable\IncludeNonNullable::endpoint());
        self::assertSame('simple', IncludeNonNullable\IncludeNonNullableResult::endpoint());
        self::assertSame('simple', IncludeNonNullable\IncludeNonNullableErrorFreeResult::endpoint());
    }

    /** Test SkipNonNullable with empty string value. */
    public function testSkipNonNullableWithEmptyString(): void
    {
        $result = SkipNonNullable\SkipNonNullable::fromStdClass((object) [
            '__typename' => 'Query',
            'nonNullable' => '',
        ]);

        self::assertSame('', $result->nonNullable);
    }

    /** Test IncludeNonNullable with empty string value. */
    public function testIncludeNonNullableWithEmptyString(): void
    {
        $result = IncludeNonNullable\IncludeNonNullable::fromStdClass((object) [
            '__typename' => 'Query',
            'nonNullable' => '',
        ]);

        self::assertSame('', $result->nonNullable);
    }

    /** Test SkipNonNullable with special characters in value. */
    public function testSkipNonNullableWithSpecialCharacters(): void
    {
        $specialValue = "Special: <>&\"'\n\t\r";
        $result = SkipNonNullable\SkipNonNullable::fromStdClass((object) [
            '__typename' => 'Query',
            'nonNullable' => $specialValue,
        ]);

        self::assertSame($specialValue, $result->nonNullable);
    }

    /** Test IncludeNonNullable with special characters in value. */
    public function testIncludeNonNullableWithSpecialCharacters(): void
    {
        $specialValue = "Unicode: \u{1F600} emoji";
        $result = IncludeNonNullable\IncludeNonNullable::fromStdClass((object) [
            '__typename' => 'Query',
            'nonNullable' => $specialValue,
        ]);

        self::assertSame($specialValue, $result->nonNullable);
    }

    /** Test SkipNonNullable make method with UNDEFINED constant. */
    public function testSkipNonNullableMakeWithUndefined(): void
    {
        $result = SkipNonNullable\SkipNonNullable::make(
            SkipNonNullable\SkipNonNullable::UNDEFINED
        );

        self::assertFalse(isset($result->nonNullable));
        self::assertNull($result->nonNullable);
    }

    /** Test IncludeNonNullable make method with UNDEFINED constant. */
    public function testIncludeNonNullableMakeWithUndefined(): void
    {
        $result = IncludeNonNullable\IncludeNonNullable::make(
            IncludeNonNullable\IncludeNonNullable::UNDEFINED
        );

        self::assertFalse(isset($result->nonNullable));
        self::assertNull($result->nonNullable);
    }

    /** Test SkipNonNullable Result with null data. */
    public function testSkipNonNullableResultWithNullData(): void
    {
        $result = SkipNonNullable\SkipNonNullableResult::fromStdClass((object) [
            'data' => null,
        ]);

        self::assertNull($result->data);
    }

    /** Test IncludeNonNullable Result with null data. */
    public function testIncludeNonNullableResultWithNullData(): void
    {
        $result = IncludeNonNullable\IncludeNonNullableResult::fromStdClass((object) [
            'data' => null,
        ]);

        self::assertNull($result->data);
    }
}