<?php declare(strict_types=1);

namespace Spawnia\Sailor\InlineFragments\Operations\InlineFragmentWithNestedNonNullableField\Search;

/**
 * @property string $__typename
 * @property \Spawnia\Sailor\InlineFragments\Operations\InlineFragmentWithNestedNonNullableField\Search\Content\ArticleContent|null $content
 */
class Article extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \Spawnia\Sailor\InlineFragments\Operations\InlineFragmentWithNestedNonNullableField\Search\Content\ArticleContent|null $content
     */
    public static function make(
        $content = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        $instance->__typename = 'Article';
        if ($content !== self::UNDEFINED) {
            $instance->__set('content', $content);
        }

        return $instance;
    }

    protected function converters(): array
    {
        /** @var array<string, \Spawnia\Sailor\Convert\TypeConverter>|null $converters */
        static $converters;

        return $converters ??= [
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'content' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\InlineFragments\Operations\InlineFragmentWithNestedNonNullableField\Search\Content\ArticleContent),
        ];
    }

    public static function endpoint(): string
    {
        return 'inline-fragments';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../sailor.php');
    }
}
