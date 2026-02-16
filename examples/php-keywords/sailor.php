<?php declare(strict_types=1);

use Spawnia\Sailor\Client;
use Spawnia\Sailor\Codegen\DirectoryFinder;
use Spawnia\Sailor\Codegen\Finder;
use Spawnia\Sailor\EndpointConfig;
use Spawnia\Sailor\PhpKeywords\Types\_abstract;
use Spawnia\Sailor\Response;
use Spawnia\Sailor\Testing\MockClient;

return [
    'php-keywords' => new class() extends EndpointConfig {
        public function namespace(): string
        {
            return 'Spawnia\Sailor\PhpKeywords';
        }

        public function targetPath(): string
        {
            return __DIR__ . '/generated';
        }

        public function schemaPath(): string
        {
            return __DIR__ . '/schema.graphql';
        }


        public function finder(): Finder
        {
            return new DirectoryFinder(__DIR__ . '/src');
        }

        public function makeClient(): Client
        {
            return new MockClient(function (string $query, ?stdClass $variables): Response {
                if (str_contains($query, 'print')) {
                    return Response::fromStdClass((object) [
                        'data' => (object) [
                            '__typename' => 'Query',
                            'print' => (object) [
                                '__typename' => 'Switch',
                                'for' => _abstract::_class,
                                'int' => 42,
                                'as' => 69,
                            ],
                        ],
                    ]);
                }

                if (str_contains($query, 'cases')) {
                    return Response::fromStdClass((object) [
                        'data' => (object) [
                            '__typename' => 'Query',
                            'cases' => [
                                (object) [
                                    '__typename' => 'Case',
                                    'id' => 'asdf',
                                ],
                            ],
                        ],
                    ]);
                }

                throw new Exception("Unexpected query: {$query}.");
            });
        }
    },
];
