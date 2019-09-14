<?php

declare(strict_types=1);

use Spawnia\Sailor\Client;
use Spawnia\Sailor\EndpointConfig;

return [
    'example' => new class implements EndpointConfig {
        public function client(): Client
        {
            return new \Spawnia\Sailor\Client\Guzzle(
                'http://example.com/graphql',
                [
                    'headers' => [
                        'Authorization' => 'Bearer foobarbaz',
                    ],
                ]
            );
        }

        public function namespace(): string
        {
            return 'Vendor\\ExampleApi';
        }

        public function targetPath(): string
        {
            return 'generated/ExampleApi';
        }

        public function searchPath(): string
        {
            return 'src';
        }
    },
];
