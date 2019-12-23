<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Spawnia\PHPUnitAssertFiles\AssertDirectory;
use Spawnia\Sailor\Codegen\Generator;
use Spawnia\Sailor\Configuration;
use Spawnia\Sailor\EndpointConfig;
use Spawnia\Sailor\Simple\MyScalarQuery;
use Spawnia\Sailor\Response;
use Spawnia\Sailor\Testing\MockClient;

class SimpleTest extends TestCase
{
    use AssertDirectory;

    const EXAMPLES_PATH = __DIR__.'/../../examples/simple/';

    public function testGeneratesFooExample(): void
    {
        $generator = new Generator($this->fooEndpoint(), 'simple');
        $generator->generate();

        self::assertDirectoryEquals(self::EXAMPLES_PATH.'expected', self::EXAMPLES_PATH.'generated');
    }

    public function testRequest(): void
    {
        $mockEndpoint = $this->fooEndpoint();

        Configuration::setEndpointConfigMap([
            'simple' => $mockEndpoint,
        ]);

        $mockClient = new MockClient();
        $mockClient->responseMocks [] = function (): Response {
            $response = new Response();
            $response->data = (object) ['scalarWithArg' => 'bar'];

            return $response;
        };
        $mockEndpoint->mockClient = $mockClient;

        $result = MyScalarQuery::execute();
        self::assertSame('bar', $result->data->scalarWithArg);
    }

    public function testRequestWithVariable(): void
    {
        $mockEndpoint = $this->fooEndpoint();

        Configuration::setEndpointConfigMap([
            'simple' => $mockEndpoint,
        ]);

        $mockClient = new MockClient();
        $mockClient->responseMocks [] = function (string $query, \stdClass $variables = null): Response {
            $response = new Response();
            $response->data = (object) ['scalarWithArg' => $variables->arg];

            return $response;
        };
        $mockEndpoint->mockClient = $mockClient;

        $result = MyScalarQuery::execute('baz');
        self::assertSame('baz', $result->data->scalarWithArg);
    }

    protected function fooEndpoint(): EndpointConfig
    {
        $fooConfig = include __DIR__.'/../../examples/simple/sailor.php';

        return $fooConfig['simple'];
    }
}
