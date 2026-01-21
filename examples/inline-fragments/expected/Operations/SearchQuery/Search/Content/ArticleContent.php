<?php declare(strict_types=1);

namespace Spawnia\Sailor\InlineFragments\Operations\SearchQuery\Search\Content;

/**
 * @property string $text
 * @property string $__typename
 */
class ArticleContent extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $text
     */
    public static function make($text): self
    {
        $instance = new self;

        if ($text !== self::UNDEFINED) {
            $instance->__set('text', $text);
        }
        $instance->__typename = 'ArticleContent';

        return $instance;
    }

    protected function converters(): array
    {
        /** @var array<string, \Spawnia\Sailor\Convert\TypeConverter>|null $converters */
        static $converters;

        return $converters ??= [
            'text' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
        ];
    }

    public static function endpoint(): string
    {
        return 'inline-fragments';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../sailor.php');
    }
}
