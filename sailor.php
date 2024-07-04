<?php declare(strict_types=1);

use Spawnia\Sailor;

return [
    'example' => new class() extends Sailor\EndpointConfig {
        public function makeClient(): Sailor\Client
        {
            // You may use one of the built-in clients, such as Guzzle, or bring your own.
            // This file is required when Sailor is first used in your application,
            // so you can configure the client dynamically, e.g. use environment variables.
            return new Sailor\Client\Guzzle(
                'https://example.com/graphql',
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
            return __DIR__ . '/generated/ExampleApi';
        }

        public function schemaPath(): string
        {
            return __DIR__ . '/example.graphql';
        }

        public function finder(): Sailor\Codegen\Finder
        {
            return new Sailor\Codegen\DirectoryFinder(__DIR__ . '/src');
        }
    },
];
