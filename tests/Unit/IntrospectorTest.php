<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit;

use GraphQL\Type\Introspection;
use GraphQL\Utils\BuildSchema;

use function Safe\file_get_contents;
use function Safe\unlink;

use Spawnia\Sailor\Client;
use Spawnia\Sailor\EndpointConfig;
use Spawnia\Sailor\Error\InvalidDataException;
use Spawnia\Sailor\Error\ResultErrorsException;
use Spawnia\Sailor\Introspector;
use Spawnia\Sailor\Json;
use Spawnia\Sailor\Response;
use Spawnia\Sailor\Testing\MockClient;
use Spawnia\Sailor\Tests\TestCase;
use stdClass;

/**
 * @phpstan-import-type ResponseMock from MockClient
 */
final class IntrospectorTest extends TestCase
{
    public const SCHEMA = /* @lang GraphQL */ <<<'GRAPHQL'
        type Query {
          simple: ID
        }

        GRAPHQL;

    public const PATH = __DIR__ . '/schema.graphql';

    /**
     * @dataProvider validResponseMocks
     *
     * @param  array<int, ResponseMock>  $responseMocks
     */
    public function testPrintsIntrospection(array $responseMocks): void
    {
        $this->makeIntrospector($responseMocks)
            ->introspect();

        self::assertFileExists(self::PATH);
        self::assertSame(self::SCHEMA, file_get_contents(self::PATH));

        unlink(self::PATH);
    }

    public function testFailsIntrospectionIfFallbackAlsoThrows(): void
    {
        self::expectException(ResultErrorsException::class);
        $this
            ->makeIntrospector([
                self::responseWithErrorsMock(),
                self::responseWithErrorsMock(),
            ])
            ->introspect();
    }

    /**
     * @return iterable<array{array<int, ResponseMock>}>
     */
    public function validResponseMocks(): iterable
    {
        yield [
            [
                self::successfulIntrospectionMock(),
            ],
        ];

        yield [
            [
                self::responseWithErrorsMock(),
                self::successfulIntrospectionMock(),
            ],
        ];

        yield [
            [
                self::misbehavedServerMock(),
                self::successfulIntrospectionMock(),
            ],
        ];
    }

    /**
     * @param array<int, ResponseMock> $responseMocks
     */
    private function makeIntrospector(array $responseMocks): Introspector
    {
        $endpointConfig = new class($responseMocks) extends EndpointConfig {
            /** @var array<int, callable> */
            private array $responseMocks;

            /**
             * @param array<int, callable> $responseMocks
             */
            public function __construct(array $responseMocks)
            {
                $this->responseMocks = $responseMocks;
            }

            public function makeClient(): Client
            {
                $mockClient = new MockClient();
                $mockClient->responseMocks = $this->responseMocks;

                return $mockClient;
            }

            public function schemaPath(): string
            {
                return IntrospectorTest::PATH;
            }

            public function namespace(): string
            {
                return 'MyScalarQuery';
            }

            public function targetPath(): string
            {
                return 'simple';
            }

            public function searchPath(): string
            {
                return 'bar';
            }
        };

        return new Introspector($endpointConfig, 'foo', 'bar');
    }

    /**
     * @return ResponseMock
     */
    public static function successfulIntrospectionMock(): callable
    {
        return static function (): Response {
            $schema = BuildSchema::build(self::SCHEMA);
            $introspection = Introspection::fromSchema($schema);

            $response = new Response();
            // @phpstan-ignore-next-line We know an associative array converts to a stdClass
            $response->data = Json::assocToStdClass($introspection);

            return $response;
        };
    }

    /**
     * @return ResponseMock
     */
    private function responseWithErrorsMock(): callable
    {
        return static function (): Response {
            $response = new Response();
            $response->errors = [(object) ['message' => 'foo']];

            return $response;
        };
    }

    /**
     * @return ResponseMock
     */
    private static function misbehavedServerMock(): callable
    {
        return static function (): Response {
            throw new InvalidDataException('misbehaved server');
        };
    }
}
