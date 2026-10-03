<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit\Codegen;

use GraphQL\Language\AST\FragmentDefinitionNode;
use GraphQL\Language\AST\NameNode;
use GraphQL\Language\AST\OperationDefinitionNode;
use PHPUnit\Framework\Attributes\DataProvider;
use Spawnia\Sailor\Codegen\Generator;
use Spawnia\Sailor\EndpointConfig;
use Spawnia\Sailor\Tests\TestCase;

final class GeneratorTest extends TestCase
{
    public function testParseNamedOperationSuccessfully(): void
    {
        $somePath = 'path';
        $documents = [
            $somePath => /* @lang GraphQL */ <<<'GRAPHQL'
                query MyScalarQuery {
                    simple
                }
                GRAPHQL,
        ];

        $parsed = Generator::parseDocuments($documents);
        self::assertCount(1, $parsed);

        $definitions = $parsed[$somePath]->definitions;
        self::assertCount(1, $definitions);

        $query = $definitions[0];
        self::assertInstanceOf(OperationDefinitionNode::class, $query);

        $nameNode = $query->name;
        self::assertInstanceOf(NameNode::class, $nameNode);
        self::assertSame('MyScalarQuery', $nameNode->value);
    }

    public function testParseFragmentSuccessfully(): void
    {
        $somePath = 'path';
        $documents = [
            $somePath => /* @lang GraphQL */ <<<'GRAPHQL'
                fragment Foo on Bar {
                    simple
                }
                GRAPHQL,
        ];

        $parsed = Generator::parseDocuments($documents);

        $fragment = $parsed[$somePath]->definitions[0];
        assert($fragment instanceof FragmentDefinitionNode);

        self::assertSame('Foo', $fragment->name->value);
    }

    public function testParseOperationsAndFragmentsSuccessfully(): void
    {
        $somePath = 'path';
        $documents = [
            $somePath => /* @lang GraphQL */ <<<'GRAPHQL'
                query FooQuery {
                    ...Foo
                }

                fragment Foo on Bar {
                    simple
                }
                GRAPHQL,
        ];

        $parsed = Generator::parseDocuments($documents);

        $query = $parsed[$somePath]->definitions[0];
        assert($query instanceof OperationDefinitionNode);

        $queryName = $query->name;
        assert($queryName instanceof NameNode);

        self::assertSame('FooQuery', $queryName->value);

        $fragment = $parsed[$somePath]->definitions[1];
        assert($fragment instanceof FragmentDefinitionNode);

        self::assertSame('Foo', $fragment->name->value);
    }

    public function testEmptyListOfDocuments(): void
    {
        self::assertSame([], Generator::parseDocuments([]));
    }

    public function testParseDocumentsThrowsErrorWithPath(): void
    {
        $path = 'thisShouldBeInTheMessage';
        $documents = [
            $path /* @lang GraphQL */ => 'invalid GraphQL',
        ];

        self::expectExceptionMessageMatches("/{$path}/");
        Generator::parseDocuments($documents);
    }

    /** @dataProvider configPaths */
    #[DataProvider('configPaths')]
    public function testConfigPath(string $configFile, string $directory, string $expected): void
    {
        $endpointConfig = \Mockery::mock(EndpointConfig::class);
        $generator = new class($endpointConfig, $configFile, 'foo') extends Generator {
            public function publicConfigPath(string $directory): string
            {
                return $this->configPath($directory);
            }
        };

        self::assertSame($expected, $generator->publicConfigPath($directory));
    }

    /** @return iterable<array{string, string, string}> */
    public static function configPaths(): iterable
    {
        yield 'unix' => [
            '/home/user/project/sailor.php',
            '/home/user/project/generated/Operations',
            "\\Safe\\realpath(__DIR__ . '/../../sailor.php')",
        ];
        yield 'windows' => [
            'C:\Users\user\project\sailor.php',
            'C:\Users\user\project/generated/Operations',
            "\\Safe\\realpath(__DIR__ . '/../../sailor.php')",
        ];
    }
}
