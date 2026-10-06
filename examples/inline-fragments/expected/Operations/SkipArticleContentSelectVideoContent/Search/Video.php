<?php declare(strict_types=1);

namespace Spawnia\Sailor\InlineFragments\Operations\SkipArticleContentSelectVideoContent\Search;

/**
 * @property \Spawnia\Sailor\InlineFragments\Operations\SkipArticleContentSelectVideoContent\Search\Content\VideoContent $content
 * @property string $__typename
 */
class Video extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \Spawnia\Sailor\InlineFragments\Operations\SkipArticleContentSelectVideoContent\Search\Content\VideoContent $content
     */
    public static function make($content): self
    {
        $instance = new self;

        if ($content !== self::UNDEFINED) {
            $instance->__set('content', $content);
        }
        $instance->__typename = 'Video';

        return $instance;
    }

    protected function converters(): array
    {
        /** @var array<string, \Spawnia\Sailor\Convert\TypeConverter>|null $converters */
        static $converters;

        return $converters ??= [
            'content' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\InlineFragments\Operations\SkipArticleContentSelectVideoContent\Search\Content\VideoContent),
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
