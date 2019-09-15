<?php

declare(strict_types=1);

use Spawnia\Sailor\Client;
use Spawnia\Sailor\Response;
use Spawnia\Sailor\EndpointConfig;
use Spawnia\Sailor\Testing\MockClient;

return [
    'foo' => new class extends EndpointConfig {
        public function namespace(): string
        {
            return 'Spawnia\Sailor\Foo';
        }

        public function targetPath(): string
        {
            return __DIR__.'/generated';
        }

        public function searchPath(): string
        {
            return __DIR__;
        }

        public function schemaPath(): string
        {
            return __DIR__.'/schema.graphqls';
        }

        public function makeClient(): Client
        {
            $mockClient = new MockClient();

            $mockClient->responseMocks [] = function (): Response {
                return Response::fromStdClass((object) [
                    'data' => (object) [
                        'foo' => 'bar',
                    ],
                ]);
            };

            return $mockClient;
        }
    },
];
