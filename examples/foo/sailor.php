<?php

declare(strict_types=1);

use Spawnia\Sailor\Client;
use Spawnia\Sailor\EndpointConfig;
use Spawnia\Sailor\Testing\MockClient;

return [
    'foo' => new class implements EndpointConfig {
        public function client(): Client
        {
            return new MockClient();
        }

        public function namespace(): string
        {
            return 'Spawnia\Sailor\Foo';
        }

        public function targetPath(): string
        {
            return 'generated';
        }

        public function searchPath(): string
        {
            return '.';
        }
    },
];
