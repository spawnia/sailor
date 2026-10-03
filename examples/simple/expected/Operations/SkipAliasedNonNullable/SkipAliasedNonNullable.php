<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\SkipAliasedNonNullable;

/**
 * @property string $nonNullable
 * @property string $__typename
 * @property string|null $skipped
 */
class SkipAliasedNonNullable extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $nonNullable
     * @param string|null $skipped
     */
    public static function make(
        $nonNullable,
        $skipped = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($nonNullable !== self::UNDEFINED) {
            $instance->__set('nonNullable', $nonNullable);
        }
        $instance->__typename = 'Query';
        if ($skipped !== self::UNDEFINED) {
            $instance->__set('skipped', $skipped);
        }

        return $instance;
    }

    protected function converters(): array
    {
        /** @var array<string, \Spawnia\Sailor\Convert\TypeConverter>|null $converters */
        static $converters;

        return $converters ??= [
            'nonNullable' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'skipped' => new \Spawnia\Sailor\Convert\OmittableConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter)),
        ];
    }

    public static function endpoint(): string
    {
        return 'simple';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../sailor.php');
    }
}
