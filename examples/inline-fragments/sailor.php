<?php declare(strict_types=1);

use Spawnia\Sailor\Client;
use Spawnia\Sailor\Codegen\DirectoryFinder;
use Spawnia\Sailor\Codegen\Finder;
use Spawnia\Sailor\EndpointConfig;
use Spawnia\Sailor\Response;
use Spawnia\Sailor\Testing\MockClient;

return [
    'inline-fragments' => new class() extends EndpointConfig {
        public function namespace(): string
        {
            return 'Spawnia\Sailor\InlineFragments';
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
            return new MockClient(static fn(): Response => Response::fromStdClass((object) [
                'data' => (object) [
                    '__typename' => 'Query',
                    'search' => [
                        (object) [
                            '__typename' => 'Article',
                            'id' => '1',
                            'title' => 'Test Article',
                            'content' => (object) [
                                '__typename' => 'ArticleContent',
                                'text' => 'Article text',
                            ],
                        ],
                        (object) [
                            '__typename' => 'Video',
                            'id' => '2',
                            'title' => 'Test Video',
                            'content' => (object) [
                                '__typename' => 'VideoContent',
                                'url' => 'https://example.com/video.mp4',
                                'duration' => 120,
                            ],
                        ],
                    ],
                ],
            ]));
        }
    },
];
