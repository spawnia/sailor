<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit;

use GraphQL\Type\Introspection;
use GraphQL\Utils\BuildSchema;
use PHPUnit\Framework\TestCase;
use Spawnia\Sailor\Client;
use Spawnia\Sailor\EndpointConfig;
use Spawnia\Sailor\Introspector;
use Spawnia\Sailor\Json;
use Spawnia\Sailor\Response;
use Spawnia\Sailor\Testing\MockClient;

class IntrospectorTest extends TestCase
{
    const SCHEMA = /* @lang GraphQL */ <<<'GRAPHQL'
type Query {
  foo: ID
}

GRAPHQL;

    const PATH = __DIR__.'/schema.graphql';

    public function testPrintsIntrospection(): void
    {
        $endpointConfig = new class extends EndpointConfig {
            public function makeClient(): Client
            {
                $mockClient = new MockClient();
                $mockClient->responseMocks[] = static function (): Response {
                    $schema = BuildSchema::build(IntrospectorTest::SCHEMA);
                    $introspection = Introspection::fromSchema($schema);

                    $response = new Response();
                    $response->data = Json::assocToStdClass($introspection);

                    return $response;
                };

                return $mockClient;
            }

            public function schemaPath(): string
            {
                return IntrospectorTest::PATH;
            }

            public function namespace(): string
            {
                return 'Foo';
            }

            public function targetPath(): string
            {
                return 'foo';
            }

            public function searchPath(): string
            {
                return 'bar';
            }
        };

        $introspector = new Introspector($endpointConfig);
        $introspector->introspect();

        self::assertFileExists(self::PATH);
        self::assertSame(self::SCHEMA, \Safe\file_get_contents(self::PATH));

        \Safe\unlink(self::PATH);
    }
}
