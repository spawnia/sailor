<?php

declare(strict_types=1);

use Spawnia\Sailor\Client;
use Spawnia\Sailor\EndpointConfig;

return [
    'example' => new class extends EndpointConfig {
        /**
         * The namespace the generated classes will be created in.
         *
         * @return string
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
            return __DIR__.'generated/ExampleApi';
        }

        /**
         * Where to look for .graphql files containing operations.
         *
         * @return string
         */
        public function searchPath(): string
        {
            return __DIR__.'src';
        }

        /**
         * The location of the schema file that describes the endpoint.
         *
         * @return string
         */
        public function schemaPath(): string
        {
            return __DIR__.'example.graphqls';
        }
    },
];
