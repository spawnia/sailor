<?php declare(strict_types=1);

namespace Spawnia\Sailor\InlineFragments\Operations\SearchQuery\Search\Content;

/**
 * @property string $url
 * @property int $duration
 * @property string $__typename
 */
class VideoContent extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $url
     * @param int $duration
     */
    public static function make($url, $duration): self
    {
        $instance = new self;

        if ($url !== self::UNDEFINED) {
            $instance->__set('url', $url);
        }
        if ($duration !== self::UNDEFINED) {
            $instance->__set('duration', $duration);
        }
        $instance->__typename = 'VideoContent';

        return $instance;
    }

    protected function converters(): array
    {
        /** @var array<string, \Spawnia\Sailor\Convert\TypeConverter>|null $converters */
        static $converters;

        return $converters ??= [
            'url' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'duration' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IntConverter),
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
