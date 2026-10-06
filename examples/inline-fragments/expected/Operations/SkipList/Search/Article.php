<?php declare(strict_types=1);

namespace Spawnia\Sailor\InlineFragments\Operations\SkipList\Search;

/**
 * @property string $title
 * @property string $__typename
 */
class Article extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $title
     */
    public static function make($title): self
    {
        $instance = new self;

        if ($title !== self::UNDEFINED) {
            $instance->__set('title', $title);
        }
        $instance->__typename = 'Article';

        return $instance;
    }

    protected function converters(): array
    {
        /** @var array<string, \Spawnia\Sailor\Convert\TypeConverter>|null $converters */
        static $converters;

        return $converters ??= [
            'title' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
