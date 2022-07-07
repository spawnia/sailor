<?php declare(strict_types=1);

use Spawnia\Sailor\Client;
use Spawnia\Sailor\EndpointConfig;
use Spawnia\Sailor\Response;
use Spawnia\Sailor\Testing\MockClient;

return [
    'php-keywords' => new class() extends EndpointConfig {
        public function namespace(): string
        {
            return 'Spawnia\Sailor\PhpKeywords';
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
                        '__typename' => 'Query',
                        'print' => (object) [
                            '__typename' => 'Switch',
                            'a' => 42,
                            'for' => 'class',
                        ],
                    ],
                ]);
            };

            return $mockClient;
        }
    },
];
