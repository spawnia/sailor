<?php declare(strict_types=1);

use GraphQL\Type\Schema;
use Spawnia\Sailor\Client;
use Spawnia\Sailor\Codegen\DirectoryFinder;
use Spawnia\Sailor\Codegen\Finder;
use Spawnia\Sailor\CustomTypes\Types\CustomEnum;
use Spawnia\Sailor\CustomTypesSrc\CustomDateTypeConfig;
use Spawnia\Sailor\CustomTypesSrc\CustomEnumTypeConfig;
use Spawnia\Sailor\CustomTypesSrc\CustomObjectTypeConfig;
use Spawnia\Sailor\EndpointConfig;
use Spawnia\Sailor\Response;
use Spawnia\Sailor\Testing\MockClient;
use Spawnia\Sailor\Type\BenSampoEnumTypeConfig;
use Spawnia\Sailor\Type\CarbonTypeConfig;

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

        public function schemaPath(): string
        {
            return __DIR__ . '/schema.graphql';
        }

        public function finder(): Finder
        {
            return new DirectoryFinder(__DIR__ . '/src');
        }

        public function makeClient(): Client
        {
            return new MockClient(function (string $query, ?stdClass $variables): Response {
                if (str_contains($query, 'withCustomEnum')) {
                    return Response::fromStdClass((object) [
                        'data' => (object) [
                            '__typename' => 'Query',
                            'withCustomEnum' => CustomEnum::B,
                        ],
                    ]);
                }

                if (str_contains($query, 'withCustomObject')) {
                    return Response::fromStdClass((object) [
                        'data' => (object) [
                            '__typename' => 'Query',
                            'withCustomObject' => (object) [
                                '__typename' => 'CustomObject',
                                'foo' => $variables->value->foo ?? null,
                            ],
                        ],
                    ]);
                }

                throw new Exception("Unexpected query: {$query}.");
            });
        }

        public function configureTypes(Schema $schema): array
        {
            return array_merge(
                parent::configureTypes($schema),
                [
                    'BenSampoEnum' => new BenSampoEnumTypeConfig($this, $schema->getType('BenSampoEnum')),
                    'CarbonDate' => new CarbonTypeConfig($this, $schema->getType('CarbonDate'), 'Y-m-d'),
                    'CustomEnum' => new CustomEnumTypeConfig($this, $schema->getType('CustomEnum')),
                    'CustomDate' => new CustomDateTypeConfig($this, $schema->getType('CustomDate')),
                    'CustomInput' => new CustomObjectTypeConfig($this, $schema->getType('CustomInput')),
                    'CustomOutput' => new CustomObjectTypeConfig($this, $schema->getType('CustomOutput')),
                ]
            );
        }
    },
];
