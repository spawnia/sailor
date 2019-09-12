<?php

declare(strict_types=1);

use Spawnia\Sailor\Client;
use Spawnia\Sailor\EndpointConfig;

return [
    'example' => new class implements EndpointConfig {
        public function client(): Client
        {
            return new \Spawnia\Sailor\GuzzleClient(
                'http://example.com/graphql',
                [
                    'headers' => [
                        'Authorization' => 'Bearer foobarbaz',
                    ],
                ]
            );
        }
    },
];
