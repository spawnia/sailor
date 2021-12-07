<?php declare(strict_types=1);

use GraphQL\Type\Schema;
use Spawnia\Sailor\Client;
use Spawnia\Sailor\EndpointConfig;
use Spawnia\Sailor\EnumSrc\CustomEnumTypeConfig;
use Spawnia\Sailor\Response;
use Spawnia\Sailor\Testing\MockClient;

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

        public function configureTypes(Schema $schema, string $endpointName): array
        {
            return array_merge(
                parent::configureTypes($schema, $endpointName),
                [
                    'CustomEnum' => new CustomEnumTypeConfig($this, $schema->getType('CustomEnum')),
                ]
            );
        }
    },
];
