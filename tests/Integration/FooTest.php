<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Integration;

use Spawnia\Sailor\Foo\Foo;
use Spawnia\Sailor\Response;
use PHPUnit\Framework\TestCase;
use Spawnia\Sailor\Configuration;
use Spawnia\Sailor\EndpointConfig;
use Spawnia\Sailor\Codegen\Generator;
use Spawnia\PHPUnitAssertFiles\AssertDirectory;
use Spawnia\Sailor\Testing\MockClient;

class FooTest extends TestCase
{
    use AssertDirectory;

    const EXAMPLES_PATH = __DIR__.'/../../examples/foo/';

    public function testGeneratesFooExample(): void
    {
        $generator = new Generator($this->fooEndpoint(), 'foo');
        $generator->generate();

        self::assertDirectoryEquals(self::EXAMPLES_PATH.'expected', self::EXAMPLES_PATH.'generated');
    }

    public function testRequest(): void
    {
        $mockEndpoint = $this->fooEndpoint();

        Configuration::setEndpointConfigMap([
            'foo' => $mockEndpoint,
        ]);

        $mockClient = new MockClient();
        $mockClient->responseMocks [] = function () {
            $response = new Response();
            $response->data = (object) ['foo' => 'bar'];

            return $response;
        };
        $mockEndpoint->mockClient = $mockClient;

        $result = Foo::execute();
        self::assertSame('bar', $result->data->foo);
    }

    protected function fooEndpoint(): EndpointConfig
    {
        $fooConfig = include __DIR__.'/../../examples/foo/sailor.php';

        return $fooConfig['foo'];
    }
}
