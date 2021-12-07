<?php declare(strict_types=1);

use Spawnia\Sailor\Client;
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
    },
];
