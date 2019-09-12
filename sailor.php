<?php

declare(strict_types=1);

use Spawnia\Sailor\Client;
use Spawnia\Sailor\GuzzleClient;
use Spawnia\Sailor\EndpointConfiguration;

return [
    'example' => new class implements EndpointConfiguration {
        public function client(): Client
        {
            return new GuzzleClient('http://example.com/graphql');
        }
    },
];
