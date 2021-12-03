<?php

declare(strict_types=1);

use GraphQL\Language\AST\DocumentNode;
use GraphQL\Type\Schema;
use Spawnia\Sailor\Client;
use Spawnia\Sailor\EndpointConfig;
use Spawnia\Sailor\EnumSrc\CustomEnumGenerator;
use Spawnia\Sailor\EnumSrc\CustomTypeConverterGenerator;
use Spawnia\Sailor\Response;
use Spawnia\Sailor\Testing\MockClient;
use Spawnia\Sailor\Type\TypeConfig;

return [
    'enum' => new class() extends EndpointConfig {
        public function namespace(): string
        {
            return 'Spawnia\Sailor\Enum';
        }

        public function targetPath(): string
        {
            return __DIR__ . '/generated';
        }

        public function searchPath(): string
        {
            return __DIR__ . '/src';
        }

        public function schemaPath(): string
        {
            return __DIR__ . '/schema.graphql';
        }

        public function makeClient(): Client
        {
            $mockClient = new MockClient();

            $mockClient->responseMocks[] = static function (): Response {
                return Response::fromStdClass((object) [
                    'data' => (object) [
                        'singleObject' => (object) [
                            'value' => 42,
                        ],
                    ],
                ]);
            };

            return $mockClient;
        }

        public function configureTypes(Schema $schema): array
        {
            return array_merge(
                parent::configureTypes($schema),
                [
                    'CustomEnum' => new TypeConfig(
                        CustomTypeConverterGenerator::className('CustomEnum', $this),
                        '\\' . CustomEnumGenerator::className('CustomEnum', $this),
                    ),
                ]
            );
        }

        public function generateClasses(Schema $schema, DocumentNode $document, string $endpointName): iterable
        {
            foreach ((new CustomEnumGenerator($schema, $document, $this, $endpointName))->generate() as $enum) {
                yield $enum;
            }

            foreach ((new CustomTypeConverterGenerator($schema, $document, $this, $endpointName))->generate() as $enum) {
                yield $enum;
            }
        }
    },
];
