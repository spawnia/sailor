<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Integration;

use Spawnia\Sailor\Error\InvalidDataException;
use Spawnia\Sailor\InlineFragments\Operations\InlineFragmentWithDirectNonNullableField;
use Spawnia\Sailor\InlineFragments\Operations\InlineFragmentWithNestedNonNullableField;
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
        self::assertNotNull($result->data->search);
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
        self::assertNotNull($result->data->search);
        $article = $result->data->search[0];
        self::assertInstanceOf(InlineFragmentWithNestedNonNullableField\Search\Article::class, $article);
        self::assertNotNull($article->content);
        self::assertSame('Article content', $article->content->text);
    }

    public function testInlineFragmentWithNestedNonNullableFieldMissing(): void
    {
        $this->expectException(InvalidDataException::class);
        $this->expectExceptionMessage('Missing field text');

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
}
