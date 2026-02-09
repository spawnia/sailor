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

    public const SCHEMA_WITH_DEPRECATED = /* @lang GraphQL */ <<<'GRAPHQL'
    type Query {
      oldField: String @deprecated(reason: "Use newField")
      newField: String
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

    public function testIncludesDeprecationsByDefault(): void
    {
        $introspector = $this->makeIntrospector(static function (): Response {
            return self::introspectionWithDeprecatedMock(true);
        });

        $introspector->introspect();

        self::assertFileExists(self::PATH);
        $schema = file_get_contents(self::PATH);
        self::assertStringContainsString('@deprecated', $schema);
        self::assertStringContainsString('Use newField', $schema);

        unlink(self::PATH);
    }

    public function testCanDisableDeprecations(): void
    {
        $introspector = $this->makeIntrospector(
            static function (): Response {
                return self::introspectionWithDeprecatedMock(false);
            },
            ['includeDeprecated' => false]
        );

        $introspector->introspect();

        self::assertFileExists(self::PATH);
        $schema = file_get_contents(self::PATH);
        self::assertStringNotContainsString('@deprecated', $schema);
        self::assertStringNotContainsString('oldField', $schema);
        self::assertStringContainsString('newField', $schema);

        unlink(self::PATH);
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
    private function makeIntrospector(callable $request, ?array $introspectionConfig = null): Introspector
    {
        $endpointConfig = new class($request, $introspectionConfig) extends EndpointConfig {
            /** @var callable */
            private $request;

            /** @var array<string, mixed>|null */
            private ?array $introspectionConfig;

            public function __construct(callable $request, ?array $introspectionConfig)
            {
                $this->request = $request;
                $this->introspectionConfig = $introspectionConfig;
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

            public function introspectionConfig(): array
            {
                return $this->introspectionConfig ?? parent::introspectionConfig();
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

    public static function introspectionWithDeprecatedMock(bool $includeDeprecated): Response
    {
        $schema = BuildSchema::build(self::SCHEMA_WITH_DEPRECATED);
        $introspection = Introspection::fromSchema($schema);
        if (! $includeDeprecated) {
            $introspection = self::stripDeprecations($introspection);
        }

        $response = new Response();
        // @phpstan-ignore-next-line We know an associative array converts to a stdClass
        $response->data = Json::assocToStdClass($introspection);

        return $response;
    }

    /**
     * @param  array<string, mixed>  $introspection
     *
     * @return array<string, mixed>
     */
    private static function stripDeprecations(array $introspection): array
    {
        if (! isset($introspection['__schema']['types']) || ! is_array($introspection['__schema']['types'])) {
            return $introspection;
        }

        foreach ($introspection['__schema']['types'] as &$type) {
            if (isset($type['fields']) && is_array($type['fields'])) {
                $type['fields'] = array_values(array_filter($type['fields'], static function (array $field): bool {
                    return empty($field['isDeprecated']);
                }));

                foreach ($type['fields'] as &$field) {
                    if (isset($field['args']) && is_array($field['args'])) {
                        $field['args'] = array_values(array_filter($field['args'], static function (array $arg): bool {
                            return empty($arg['isDeprecated']);
                        }));
                    }
                }
                unset($field);
            }

            if (isset($type['enumValues']) && is_array($type['enumValues'])) {
                $type['enumValues'] = array_values(array_filter($type['enumValues'], static function (array $enumValue): bool {
                    return empty($enumValue['isDeprecated']);
                }));
            }
        }
        unset($type);

        return $introspection;
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
