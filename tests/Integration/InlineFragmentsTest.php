<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Integration;

use Spawnia\Sailor\Error\InvalidDataException;
use Spawnia\Sailor\InlineFragments\Operations\InlineFragmentWithDirectNonNullableField;
use Spawnia\Sailor\InlineFragments\Operations\InlineFragmentWithNestedNonNullableField;
use Spawnia\Sailor\InlineFragments\Operations\SkipInterfaceField;
use Spawnia\Sailor\InlineFragments\Operations\SkipList;
use Spawnia\Sailor\Tests\TestCase;

final class InlineFragmentsTest extends TestCase
{
    public function testInlineFragmentWithDirectNonNullableFieldOmitted(): void
    {
        $result = InlineFragmentWithDirectNonNullableField\InlineFragmentWithDirectNonNullableFieldResult::fromStdClass((object) [
            'data' => (object) [
                '__typename' => 'Query',
                'search' => [
                    (object) [
                        '__typename' => 'Article',
                    ],
                ],
            ],
        ]);

        self::assertNotNull($result->data);
        $article = $result->data->search[0];
        self::assertInstanceOf(InlineFragmentWithDirectNonNullableField\Search\Article::class, $article);
        self::assertNull($article->title);
    }

    public function testInlineFragmentWithDirectNonNullableFieldPresent(): void
    {
        $result = InlineFragmentWithDirectNonNullableField\InlineFragmentWithDirectNonNullableFieldResult::fromStdClass((object) [
            'data' => (object) [
                '__typename' => 'Query',
                'search' => [
                    (object) [
                        '__typename' => 'Article',
                        'title' => 'Test Article',
                    ],
                ],
            ],
        ]);

        self::assertNotNull($result->data);
        $article = $result->data->search[0];
        self::assertInstanceOf(InlineFragmentWithDirectNonNullableField\Search\Article::class, $article);
        self::assertSame('Test Article', $article->title);
    }

    public function testInlineFragmentWithNestedNonNullableFieldOmitted(): void
    {
        $result = InlineFragmentWithNestedNonNullableField\InlineFragmentWithNestedNonNullableFieldResult::fromStdClass((object) [
            'data' => (object) [
                '__typename' => 'Query',
                'search' => [
                    (object) [
                        '__typename' => 'Article',
                    ],
                ],
            ],
        ]);

        self::assertNotNull($result->data);
        $article = $result->data->search[0];
        self::assertInstanceOf(InlineFragmentWithNestedNonNullableField\Search\Article::class, $article);
        self::assertNull($article->content);
    }

    public function testInlineFragmentWithNestedNonNullableFieldPresent(): void
    {
        $result = InlineFragmentWithNestedNonNullableField\InlineFragmentWithNestedNonNullableFieldResult::fromStdClass((object) [
            'data' => (object) [
                '__typename' => 'Query',
                'search' => [
                    (object) [
                        '__typename' => 'Article',
                        'content' => (object) [
                            '__typename' => 'ArticleContent',
                            'text' => 'Article content',
                        ],
                    ],
                ],
            ],
        ]);

        self::assertNotNull($result->data);
        $article = $result->data->search[0];
        self::assertInstanceOf(InlineFragmentWithNestedNonNullableField\Search\Article::class, $article);
        self::assertNotNull($article->content);
        self::assertSame('Article content', $article->content->text);
    }

    public function testInlineFragmentWithNestedNonNullableFieldMissing(): void
    {
        $this->expectExceptionObject(new InvalidDataException('inline-fragments: Invalid value for field search. inline-fragments: Invalid value for field content. inline-fragments: Missing field text.'));

        InlineFragmentWithNestedNonNullableField\InlineFragmentWithNestedNonNullableFieldResult::fromStdClass((object) [
            'data' => (object) [
                '__typename' => 'Query',
                'search' => [
                    (object) [
                        '__typename' => 'Article',
                        'content' => (object) [
                            '__typename' => 'ArticleContent',
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function testSkippedListOmittedByServer(): void
    {
        $result = SkipList\SkipListResult::fromStdClass((object) [
            'data' => (object) [
                '__typename' => 'Query',
            ],
        ]);

        self::assertNotNull($result->data);
        self::assertNull($result->data->search);
    }

    public function testSkippedListRejectsNullItems(): void
    {
        $this->expectExceptionObject(new InvalidDataException('inline-fragments: Invalid value for field search. Expected non-null value, got null'));
        SkipList\SkipListResult::fromStdClass((object) [
            'data' => (object) [
                '__typename' => 'Query',
                'search' => [null],
            ],
        ]);
    }

    public function testSkippedInterfaceFieldOmittedByServerForAllTypes(): void
    {
        $result = SkipInterfaceField\SkipInterfaceFieldResult::fromStdClass((object) [
            'data' => (object) [
                '__typename' => 'Query',
                'search' => [
                    (object) ['__typename' => 'Article'],
                    (object) ['__typename' => 'Video'],
                ],
            ],
        ]);

        self::assertNotNull($result->data);
        [$article, $video] = $result->data->search;
        self::assertInstanceOf(SkipInterfaceField\Search\Article::class, $article);
        self::assertNull($article->id);
        self::assertInstanceOf(SkipInterfaceField\Search\Video::class, $video);
        self::assertNull($video->id);
    }
}
