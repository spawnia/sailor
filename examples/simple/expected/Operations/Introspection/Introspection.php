<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\Introspection;

/**
 * @property \Spawnia\Sailor\Simple\Operations\Introspection\__schema\__Schema $__schema
 * @property string $__typename
 * @property \Spawnia\Sailor\Simple\Operations\Introspection\__type\__Type|null $__type
 */
class Introspection extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \Spawnia\Sailor\Simple\Operations\Introspection\__schema\__Schema $__schema
     * @param \Spawnia\Sailor\Simple\Operations\Introspection\__type\__Type|null $__type
     */
    public static function make(
        $__schema,
        $__type = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($__schema !== self::UNDEFINED) {
            $instance->__set('__schema', $__schema);
        }
        $instance->__typename = 'Query';
        if ($__type !== self::UNDEFINED) {
            $instance->__set('__type', $__type);
        }

        return $instance;
    }

    protected function converters(): array
    {
        /** @var array<string, \Spawnia\Sailor\Convert\TypeConverter>|null $converters */
        static $converters;

        return $converters ??= [
            '__schema' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Simple\Operations\Introspection\__schema\__Schema),
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            '__type' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Simple\Operations\Introspection\__type\__Type),
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
