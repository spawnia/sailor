<?php declare(strict_types=1);

use Spawnia\Sailor\Client;
use Spawnia\Sailor\Codegen\DirectoryFinder;
use Spawnia\Sailor\Codegen\Finder;
use Spawnia\Sailor\EndpointConfig;
use Spawnia\Sailor\Response;
use Spawnia\Sailor\Testing\MockClient;

return [
    'simple' => new class() extends EndpointConfig {
        public function namespace(): string
        {
            return 'Spawnia\Sailor\Simple';
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
            return new MockClient(static fn(): Response => Response::fromStdClass((object) [
                'data' => (object) [
                    '__typename' => 'Query',
                    'singleObject' => (object) [
                        '__typename' => 'SomeObject',
                        'value' => 42,
                    ],
                ],
            ]));
        }
    },
];
