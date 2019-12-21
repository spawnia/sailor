<?php

declare(strict_types=1);

use Spawnia\Sailor\Client;
use Spawnia\Sailor\EndpointConfig;

/**
 * This must return a map from endpoint names to EndpointConfig classes.
 */
return [
    'example' => new class extends EndpointConfig {

        /**
         * Instantiate a client for Sailor to use for querying.
         *
         * You may use one of the built-in clients, such as Guzzle, or
         * bring your own implementation.
         *
         * Configuring the client is up to you. Since this configuration
         * file is just PHP code, you can do anything. For example, you
         * can use environment variables to enable a dynamic config.
         *
         * @return \Spawnia\Sailor\Client
         */
        public function makeClient(): Client
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

        /**
         * The namespace the generated classes will be created in.
         *
         * @return string
         */
        public function namespace(): string
        {
            return 'Vendor\\ExampleApi';
        }

        /**
         * Path to the directory where the generated classes will be put.
         *
         * @return string
         */
        public function targetPath(): string
        {
            return __DIR__.'/generated/ExampleApi';
        }

        /**
         * Where to look for .graphql files containing operations.
         *
         * @return string
         */
        public function searchPath(): string
        {
            return __DIR__.'/src';
        }

        /**
         * The location of the schema file that describes the endpoint.
         *
         * @return string
         */
        public function schemaPath(): string
        {
            return __DIR__.'/example.graphqls';
        }
    },
];
