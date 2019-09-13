<?php

declare(strict_types=1);

use Spawnia\Sailor\Client;
use Spawnia\Sailor\EndpointConfig;

global $mock;
$mock = new \Spawnia\Sailor\Testing\MockClient();

return [
    'example' => new class implements EndpointConfig {
        public function client(): Client
        {
            global $mock;

            return $mock;
        }

        public function namespace(): string
        {
            // TODO: Implement namespace() method.
        }

        public function targetPath(): string
        {
            // TODO: Implement targetPath() method.
        }

        public function searchPath(): string
        {
            // TODO: Implement searchPath() method.
        }
    },
];
