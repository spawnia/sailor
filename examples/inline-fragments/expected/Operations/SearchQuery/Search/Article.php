<?php declare(strict_types=1);

namespace Spawnia\Sailor\InlineFragments\Operations\SearchQuery\Search;

/**
 * @property string $id
 * @property string $title
 * @property \Spawnia\Sailor\InlineFragments\Operations\SearchQuery\Search\Content\ArticleContent $content
 * @property string $__typename
 */
class Article extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $id
     * @param string $title
     * @param \Spawnia\Sailor\InlineFragments\Operations\SearchQuery\Search\Content\ArticleContent $content
     */
    public static function make($id, $title, $content): self
    {
        $instance = new self;

        if ($id !== self::UNDEFINED) {
            $instance->__set('id', $id);
        }
        if ($title !== self::UNDEFINED) {
            $instance->__set('title', $title);
        }
        if ($content !== self::UNDEFINED) {
            $instance->__set('content', $content);
        }
        $instance->__typename = 'Article';

        return $instance;
    }

    protected function converters(): array
    {
        /** @var array<string, \Spawnia\Sailor\Convert\TypeConverter>|null $converters */
        static $converters;

        return $converters ??= [
            'id' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'title' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'content' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\InlineFragments\Operations\SearchQuery\Search\Content\ArticleContent),
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
