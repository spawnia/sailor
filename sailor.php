<?php declare(strict_types=1);

use Spawnia\Sailor\Codegen\DirectoryFinder;
use Spawnia\Sailor\Codegen\Finder;
use Spawnia\Sailor\EndpointConfig;

/*
 * This must return a map from endpoint names to EndpointConfig classes.
 */
return [
    'example' => new class() extends EndpointConfig {
        /**
         * Instantiate a client for Sailor to use for querying.
         *
         * You may use one of the built-in clients, such as Guzzle, or
         * bring your own implementation.
         *
         * Configuring the client is up to you. Since this configuration
         * file is just PHP code, you can do anything. For example, you
         * can use environment variables to enable a dynamic config.
         */
        public function makeClient(): Spawnia\Sailor\Client
        {
            return new Spawnia\Sailor\Client\Guzzle(
                'https://example.com/graphql',
                [
                    'headers' => [
                        'Authorization' => 'Bearer foobarbaz',
                    ],
                ]
            );
        }

        /** The namespace the generated classes will be created in. */
        public function namespace(): string
        {
            return 'Vendor\\ExampleApi';
        }

        /** Path to the directory where the generated classes will be put. */
        public function targetPath(): string
        {
            return __DIR__ . '/generated/ExampleApi';
        }

        /** The location of the schema file that describes the endpoint. */
        public function schemaPath(): string
        {
            return __DIR__ . '/example.graphql';
        }

        /** Instantiate a class to find GraphQL documents. */
        public function finder(): Finder
        {
            return new DirectoryFinder(__DIR__ . '/src');
        }
    },
];
