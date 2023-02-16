<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit;

use GraphQL\Type\Introspection;
use GraphQL\Utils\BuildSchema;
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

use function Safe\file_get_contents;
use function Safe\unlink;

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
     * @param  ResponseMock  $respond
     */
    public function testPrintsIntrospection(callable $respond): void
    {
        $this->makeIntrospector($respond)
            ->introspect();

        self::assertFileExists(self::PATH);
        self::assertSame(self::SCHEMA, file_get_contents(self::PATH));

        unlink(self::PATH);
    }

    public function testFailsIntrospectionIfFallbackAlsoThrows(): void
    {
        self::expectException(ResultErrorsException::class);
        $this
            ->makeIntrospector(static fn (): Response => self::responseWithErrorsMock())
            ->introspect();
    }

    /**
     * @return iterable<array{ResponseMock}>
     */
    public function validResponseMocks(): iterable
    {
        yield [
            static fn(): Response => self::successfulIntrospectionMock(),
        ];

        yield [
            static function(): Response {
                static $called = false;
                $response = $called ? self::responseWithErrorsMock() : self::successfulIntrospectionMock();
                $called = true;
                return $response;
            }
        ];

        yield [
            static function(): Response {
                static $called = false;
                $response = $called ? self::misbehavedServerMock() : self::successfulIntrospectionMock();
                $called = true;
                return $response;
            }
        ];
    }

    /**
     * @param ResponseMock $respond
     */
    private function makeIntrospector(callable $respond): Introspector
    {
        $endpointConfig = new class($respond) extends EndpointConfig {
            /** @var ResponseMock */
            private $respond;

            /**
             * @param ResponseMock $respond
             */
            public function __construct(callable $respond)
            {
                $this->respond = $respond;
            }

            public function makeClient(): Client
            {
                return new MockClient($this->respond);
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

    public static function successfulIntrospectionMock(): Response
    {
        $schema = BuildSchema::build(self::SCHEMA);
        $introspection = Introspection::fromSchema($schema);

        $response = new Response();
        // @phpstan-ignore-next-line We know an associative array converts to a stdClass
        $response->data = Json::assocToStdClass($introspection);

        return $response;
    }

    private static function responseWithErrorsMock(): Response
    {
        $response = new Response();
        $response->errors = [(object) ['message' => 'foo']];

        return $response;
    }

    private static function misbehavedServerMock(): Response
    {
        throw new InvalidDataException('misbehaved server');
    }
}
