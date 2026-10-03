<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Integration;

use Spawnia\Sailor\Client;
use Spawnia\Sailor\Codegen\Finder;
use Spawnia\Sailor\Codegen\Generator;
use Spawnia\Sailor\EndpointConfig;
use Spawnia\Sailor\Tests\Examples;
use Spawnia\Sailor\Tests\TestCase;

final class ClientDirectivesCodegenTest extends TestCase
{
    public function testRejectsDirectiveOnFragmentSpread(): void
    {
        $this->expectExceptionMessage('Sailor only supports @skip and @include on fields, found @skip on fragment spread ...TwoArgs.');
        self::generate(/** @lang GraphQL */ '
            query Foo($value: Boolean!) {
                ...TwoArgs @skip(if: $value)
            }

            fragment TwoArgs on Query {
                twoArgs
            }
        ');
    }

    public function testRejectsDirectiveOnInlineFragment(): void
    {
        $this->expectExceptionMessage('Sailor only supports @skip and @include on fields, found @include on inline fragment on Query.');
        self::generate(/** @lang GraphQL */ '
            query Foo($value: Boolean!) {
                ... on Query @include(if: $value) {
                    twoArgs
                }
            }
        ');
    }

    public function testRejectsDirectiveOnInlineFragmentWithoutTypeCondition(): void
    {
        $this->expectExceptionMessage('Sailor only supports @skip and @include on fields, found @skip on inline fragment.');
        self::generate(/** @lang GraphQL */ '
            query Foo($value: Boolean!) {
                ... @skip(if: $value) {
                    twoArgs
                }
            }
        ');
    }

    public function testRejectsDirectiveOnFieldSelectedAgain(): void
    {
        $this->expectExceptionMessage('Sailor only supports @skip and @include on fields selected once, found @skip on Foo.singleObject which is selected 2 times. Give the selections distinct aliases.');
        self::generate(/** @lang GraphQL */ '
            query Foo($value: Boolean!) {
                singleObject @skip(if: $value) {
                    value
                }
                singleObject {
                    nested {
                        value
                    }
                }
            }
        ');
    }

    public function testRejectsDirectiveOnFieldSelectedAgainThroughFragment(): void
    {
        $this->expectExceptionMessage('Sailor only supports @skip and @include on fields selected once, found @include on Foo.singleObject.value which is selected 2 times. Give the selections distinct aliases.');
        self::generate(/** @lang GraphQL */ '
            query Foo($value: Boolean!) {
                singleObject {
                    value @include(if: $value)
                    ...Value
                }
            }

            fragment Value on SomeObject {
                value
            }
        ');
    }

    public function testRejectsDirectiveOnFieldWhoseParentIsSelectedAgain(): void
    {
        $this->expectExceptionMessage('Sailor only supports @skip and @include on fields selected once, found @skip on Foo.singleObject.nested which is selected 2 times. Give the selections distinct aliases.');
        self::generate(/** @lang GraphQL */ '
            query Foo($value: Boolean!) {
                singleObject {
                    nested @skip(if: $value) {
                        value
                    }
                }
                singleObject {
                    nested {
                        nested {
                            value
                        }
                    }
                }
            }
        ');
    }

    protected static function generate(string $document): void
    {
        $simple = Examples::examplePath('simple');
        $endpoint = new class($document, "{$simple}/schema.graphql") extends EndpointConfig {
            private string $document;

            private string $schemaPath;

            public function __construct(string $document, string $schemaPath)
            {
                $this->document = $document;
                $this->schemaPath = $schemaPath;
            }

            public function makeClient(): Client
            {
                throw new \Exception('Not needed for codegen.');
            }

            public function namespace(): string
            {
                return 'Spawnia\Sailor\ClientDirectives';
            }

            public function targetPath(): string
            {
                return '/dev/null';
            }

            public function schemaPath(): string
            {
                return $this->schemaPath;
            }

            public function finder(): Finder
            {
                return new class($this->document) implements Finder {
                    private string $document;

                    public function __construct(string $document)
                    {
                        $this->document = $document;
                    }

                    public function documents(): array
                    {
                        return ['document.graphql' => $this->document];
                    }
                };
            }
        };

        iterator_to_array((new Generator($endpoint, "{$simple}/sailor.php", 'simple'))->generate(), false);
    }
}
