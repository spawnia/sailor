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
use Spawnia\Sailor\Type\NativeEnumTypeConfig;

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

            $types = parent::configureTypes($schema);

            $types['BenSampoEnum'] = new BenSampoEnumTypeConfig($this, $schema->getType('BenSampoEnum'));
            $types['CarbonDate'] = new CarbonTypeConfig($this, $schema->getType('CarbonDate'), 'Y-m-d');
            $types['CustomEnum'] = new CustomEnumTypeConfig($this, $schema->getType('CustomEnum'));
            $types['CustomDate'] = new CustomDateTypeConfig($this, $schema->getType('CustomDate'));
            $types['CustomInput'] = new CustomObjectTypeConfig($this, $schema->getType('CustomInput'));
            $types['CustomOutput'] = new CustomObjectTypeConfig($this, $schema->getType('CustomOutput'));

            // if we are running on PHP 8.1 or higher, we can use the native enum type
            if (phpversion() >= '8.1') {
                $types['NativeEnum'] = new NativeEnumTypeConfig($this, $schema->getType('NativeEnum'));
            }

            return $types;
        }
    },
];
