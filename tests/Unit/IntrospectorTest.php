<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit;

use GraphQL\Type\Introspection;
use GraphQL\Utils\BuildSchema;
use Spawnia\Sailor\Client;
use Spawnia\Sailor\Codegen\DirectoryFinder;
use Spawnia\Sailor\Codegen\Finder;
use Spawnia\Sailor\EndpointConfig;
use Spawnia\Sailor\Error\InvalidDataException;
use Spawnia\Sailor\Error\ResultErrorsException;
use Spawnia\Sailor\Introspector;
use Spawnia\Sailor\Json;
use Spawnia\Sailor\Response;
use Spawnia\Sailor\Testing\MockClient;
use Spawnia\Sailor\Tests\TestCase;

use function Safe\file_get_contents;
use function Safe\unlink;

/** @phpstan-import-type Request from MockClient */
final class IntrospectorTest extends TestCase
{
    public const SCHEMA = /* @lang GraphQL */ <<<'GRAPHQL'
    type Query {
      simple: ID
    }

    GRAPHQL;

    public const SCHEMA_WITH_DEPRECATED_INPUT_VALUES = /* @lang GraphQL */ <<<'GRAPHQL'
    directive @example(old: String @deprecated(reason: "Use new"), new: String) on FIELD

    input Filter {
      old: String @deprecated(reason: "Use new")
      new: String
    }

    type Query {
      simple(old: String @deprecated(reason: "Use new"), input: Filter): ID
    }

    GRAPHQL;

    public const PATH = __DIR__ . '/schema.graphql';

    /**
     * @dataProvider validRequests
     *
     * @param  Request  $request
     */
    public function testPrintsIntrospection(callable $request): void
    {
        $this->makeIntrospector($request)
            ->introspect();

        self::assertFileExists(self::PATH);
        self::assertSame(self::SCHEMA, file_get_contents(self::PATH));

        unlink(self::PATH);
    }

    public function testPrintsDeprecatedInputValues(): void
    {
        $this->makeIntrospector(
            static fn (): Response => self::successfulIntrospectionMock(self::SCHEMA_WITH_DEPRECATED_INPUT_VALUES)
        )->introspect();

        self::assertFileExists(self::PATH);
        self::assertSame(self::SCHEMA_WITH_DEPRECATED_INPUT_VALUES, file_get_contents(self::PATH));

        unlink(self::PATH);
    }

    public function testFailsIntrospectionIfFallbackAlsoThrows(): void
    {
        self::expectException(ResultErrorsException::class);
        $this
            ->makeIntrospector(static fn (): Response => self::responseWithErrorsMock())
            ->introspect();
    }

    /** @return iterable<array{Request}> */
    public static function validRequests(): iterable
    {
        yield [
            static fn (): Response => self::successfulIntrospectionMock(),
        ];

        yield [
            static function (): Response {
                static $called = false;
                $response = $called
                    ? self::responseWithErrorsMock()
                    : self::successfulIntrospectionMock();
                $called = true;

                return $response;
            },
        ];

        yield [
            static function (): Response {
                static $called = false;
                $response = $called
                    ? self::misbehavedServerMock()
                    : self::successfulIntrospectionMock();
                $called = true;

                return $response;
            },
        ];
    }

    /** @param Request $request */
    private function makeIntrospector(callable $request): Introspector
    {
        $endpointConfig = new class($request) extends EndpointConfig {
            /** @var callable */
            private $request;

            public function __construct(callable $request)
            {
                $this->request = $request;
            }

            public function makeClient(): Client
            {
                return new MockClient($this->request);
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

            public function finder(): Finder
            {
                return new DirectoryFinder('bar');
            }
        };

        return new Introspector($endpointConfig, 'foo', 'bar');
    }

    public static function successfulIntrospectionMock(string $schemaString = self::SCHEMA): Response
    {
        $schema = BuildSchema::build($schemaString);
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
