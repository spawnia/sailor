<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit;

use GraphQL\GraphQL;
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

    public function testFailsIntrospectionIfFallbackAlsoThrows(): void
    {
        self::expectException(ResultErrorsException::class);
        $this
            ->makeIntrospector(static fn (): Response => self::responseWithErrorsMock())
            ->introspect();
    }

    public function testRetainsSupportedIntrospectionMetadataWhenOneOfIsUnsupported(): void
    {
        if (strpos(Introspection::getIntrospectionQuery(['typeIsOneOf' => true]), 'isOneOf') === false) {
            self::markTestSkipped('The installed graphql-php cannot request isOneOf.');
        }

        $schema = BuildSchema::build(<<<'GRAPHQL'
        directive @tag repeatable on FIELD_DEFINITION

        type Query {
          today: String
        }
        GRAPHQL);
        $queries = [];
        $this
            ->makeIntrospector(static function (string $query) use ($schema, &$queries): Response {
                $queries[] = $query;

                return strpos($query, 'isOneOf') !== false
                    ? self::responseWithErrorsMock()
                    : Response::fromExecutionResult(GraphQL::executeQuery($schema, $query));
            })
            ->introspect();
        $printed = file_get_contents(self::PATH);
        unlink(self::PATH);

        self::assertCount(2, $queries);
        self::assertStringContainsString('isOneOf', $queries[0]);
        self::assertStringNotContainsString('isOneOf', $queries[1]);
        self::assertStringContainsString('specifiedByURL', $queries[1]);
        self::assertStringContainsString('isRepeatable', $queries[1]);
        self::assertStringContainsString('directive @tag repeatable on FIELD_DEFINITION', $printed);
    }

    public function testFallsBackWhenOptionalIntrospectionFieldsAreUnsupported(): void
    {
        $extended = Introspection::getIntrospectionQuery([
            'directiveIsRepeatable' => true,
            'specifiedByURL' => true,
        ]);
        $withOneOf = Introspection::getIntrospectionQuery([
            'directiveIsRepeatable' => true,
            'specifiedByURL' => true,
            'typeIsOneOf' => true,
        ]);
        $legacy = Introspection::getIntrospectionQuery([
            'directiveIsRepeatable' => false,
            'specifiedByURL' => false,
        ]);
        $queries = [];
        $this
            ->makeIntrospector(static function (string $query) use (&$queries, $legacy): Response {
                $queries[] = $query;

                return $query === $legacy
                    ? self::successfulIntrospectionMock()
                    : self::responseWithErrorsMock();
            })
            ->introspect();
        unlink(self::PATH);

        self::assertSame($withOneOf === $extended ? [$extended, $legacy] : [$withOneOf, $extended, $legacy], $queries);
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
                $response = $called === true
                    ? self::responseWithErrorsMock()
                    : self::successfulIntrospectionMock();
                $called = true;

                return $response;
            },
        ];

        yield [
            static function (): Response {
                static $called = false;
                $response = $called === true
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
