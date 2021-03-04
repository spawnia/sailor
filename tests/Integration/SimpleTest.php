<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Integration;

use Spawnia\PHPUnitAssertFiles\AssertDirectory;
use Spawnia\Sailor\Codegen\Generator;
use Spawnia\Sailor\Codegen\Writer;
use Spawnia\Sailor\Configuration;
use Spawnia\Sailor\EndpointConfig;
use Spawnia\Sailor\Response;
use Spawnia\Sailor\Simple\MyObjectNestedQuery;
use Spawnia\Sailor\Simple\MyScalarQuery;
use Spawnia\Sailor\Simple\MyScalarQuery\MyScalarQueryResult;
use Spawnia\Sailor\Testing\MockClient;
use Spawnia\Sailor\Tests\TestCase;

class SimpleTest extends TestCase
{
    use AssertDirectory;

    const EXAMPLES_PATH = __DIR__.'/../../examples/simple/';

    public function testGeneratesFooExample(): void
    {
        $endpoint = $this->fooEndpoint();
        $generator = new Generator($endpoint, 'simple');
        $files = $generator->generate();
        $writer = new Writer($endpoint);
        $writer->write($files);

        self::assertDirectoryEquals(self::EXAMPLES_PATH.'expected', self::EXAMPLES_PATH.'generated');
    }

    public function testRequest(): void
    {
        $mockEndpoint = $this->fooEndpoint();

        Configuration::setEndpointConfigMap([
            'simple' => $mockEndpoint,
        ]);

        $mockClient = new MockClient();
        $mockClient->responseMocks [] = static function (): Response {
            $response = new Response();
            $response->data = (object) ['scalarWithArg' => 'bar'];

            return $response;
        };
        $mockEndpoint->mockClient = $mockClient;

        $result = MyScalarQuery::execute();
        self::assertSame('bar', $result->data->scalarWithArg);
    }

    public function testMockResult(): void
    {
        $bar = 'bar';

        MyScalarQuery::mock()
            ->expects('execute')
            ->with()
            ->andReturn(MyScalarQueryResult::fromStdClass((object) [
                'data' => (object) [
                    'scalarWithArg' => $bar,
                ],
            ]));

        self::assertSame($bar, MyScalarQuery::execute()->data->scalarWithArg);
    }

    public function testRequestWithVariable(): void
    {
        $mockEndpoint = $this->fooEndpoint();

        Configuration::setEndpointConfigMap([
            'simple' => $mockEndpoint,
        ]);

        $mockClient = new MockClient();
        $mockClient->responseMocks [] = static function (string $query, \stdClass $variables = null): Response {
            $response = new Response();
            $response->data = (object) ['scalarWithArg' => $variables->arg];

            return $response;
        };
        $mockEndpoint->mockClient = $mockClient;

        $result = MyScalarQuery::execute('baz');
        self::assertSame('baz', $result->data->scalarWithArg);
    }

    public function testRequestError(): void
    {
        $mockEndpoint = $this->fooEndpoint();

        Configuration::setEndpointConfigMap([
            'simple' => $mockEndpoint,
        ]);

        $mockClient = new MockClient();
        $mockClient->responseMocks [] = static function (): Response {
            $response = new Response();
            $response->data = null;
            $response->errors = [
                (object) ['message' => 'some error'],
            ];

            return $response;
        };
        $mockEndpoint->mockClient = $mockClient;

        $result = MyScalarQuery::execute();
        $errors = $result->errors;
        self::assertNotNull($errors);
        self::assertSame('some error', $errors[0]->message);
    }

    public function testNestedObject(): void
    {
        $mockEndpoint = $this->fooEndpoint();

        Configuration::setEndpointConfigMap([
            'simple' => $mockEndpoint,
        ]);

        $mockClient = new MockClient();
        $mockClient->responseMocks [] = static function (): Response {
            $response = new Response();
            $response->data = (object) [
                'singleObject' => (object) [
                    'nested' => (object) [
                        'value' => 42,
                    ],
                ],
            ];

            return $response;
        };
        $mockEndpoint->mockClient = $mockClient;

        $result = MyObjectNestedQuery::execute();
        $object = $result->data->singleObject;
        self::assertNotNull($object);

        $nested = $object->nested;
        self::assertNotNull($nested);
        self::assertSame(42, $nested->value);
    }

    public function testNestedObjectNull(): void
    {
        $mockEndpoint = $this->fooEndpoint();

        Configuration::setEndpointConfigMap([
            'simple' => $mockEndpoint,
        ]);

        $mockClient = new MockClient();
        $mockClient->responseMocks [] = static function (): Response {
            $response = new Response();
            $response->data = (object) [
                'singleObject' => (object) [
                    'nested' => null,
                ],
            ];

            return $response;
        };
        $mockEndpoint->mockClient = $mockClient;

        $result = MyObjectNestedQuery::execute();
        $object = $result->data->singleObject;
        self::assertNotNull($object);
        self::assertNull($object->nested);
    }

    protected function fooEndpoint(): EndpointConfig
    {
        $fooConfig = include __DIR__.'/../../examples/simple/sailor.php';

        return $fooConfig['simple'];
    }
}
