<?php declare(strict_types=1);

use GraphQL\Type\Schema;
use Spawnia\Sailor\Client;
use Spawnia\Sailor\CustomTypes\Types\CustomEnum;
use Spawnia\Sailor\CustomTypesSrc\CustomDateTypeConfig;
use Spawnia\Sailor\CustomTypesSrc\CustomEnumTypeConfig;
use Spawnia\Sailor\CustomTypesSrc\CustomObjectTypeConfig;
use Spawnia\Sailor\EndpointConfig;
use Spawnia\Sailor\Response;
use Spawnia\Sailor\Testing\MockClient;
use Spawnia\Sailor\Type\BenSampoEnumTypeConfig;

return [
    'custom-types' => new class() extends EndpointConfig {
        public function namespace(): string
        {
            return 'Spawnia\Sailor\CustomTypes';
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

            $mockClient->responseMocks[] = static fn (): Response => Response::fromStdClass((object) [
                'data' => (object) [
                    '__typename' => 'Query',
                    'withCustomEnum' => CustomEnum::B,
                ],
            ]);
            $mockClient->responseMocks[] = static fn (string $query, ?\stdClass $variables): Response => Response::fromStdClass((object) [
                'data' => (object) [
                    '__typename' => 'Query',
                    'withCustomObject' => (object) [
                        '__typename' => 'CustomObject',
                        'foo' => $variables->value->foo ?? null,
                    ],
                ],
            ]);

            return $mockClient;
        }

        public function configureTypes(Schema $schema): array
        {
            return array_merge(
                parent::configureTypes($schema),
                [
                    'BenSampoEnum' => new BenSampoEnumTypeConfig($this, $schema->getType('BenSampoEnum')),
                    'CustomEnum' => new CustomEnumTypeConfig($this, $schema->getType('CustomEnum')),
                    'CustomDate' => new CustomDateTypeConfig($this, $schema->getType('CustomDate')),
                    'CustomInput' => new CustomObjectTypeConfig($this, $schema->getType('CustomInput')),
                    'CustomOutput' => new CustomObjectTypeConfig($this, $schema->getType('CustomOutput')),
                ]
            );
        }
    },
];
