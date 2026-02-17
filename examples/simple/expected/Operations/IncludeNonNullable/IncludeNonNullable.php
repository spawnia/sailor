<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\IncludeNonNullable;

/**
 * @property string $__typename
 * @property string|null $nonNullable
 */
class IncludeNonNullable extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $nonNullable
     */
    public static function make(
        $nonNullable = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        $instance->__typename = 'Query';
        if ($nonNullable !== self::UNDEFINED) {
            $instance->__set('nonNullable', $nonNullable);
        }

        return $instance;
    }

    protected function converters(): array
    {
        /** @var array<string, \Spawnia\Sailor\Convert\TypeConverter>|null $converters */
        static $converters;

        return $converters ??= [
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'nonNullable' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
