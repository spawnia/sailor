<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\SkipBeforeRequiredNonNullable;

/**
 * @property string $required
 * @property string $__typename
 * @property string|null $nonNullable
 */
class SkipBeforeRequiredNonNullable extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $required
     * @param string|null $nonNullable
     */
    public static function make(
        $required,
        $nonNullable = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($required !== self::UNDEFINED) {
            $instance->__set('required', $required);
        }
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
            'required' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'nonNullable' => new \Spawnia\Sailor\Convert\OmittableConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter)),
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
