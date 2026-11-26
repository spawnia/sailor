<?php declare(strict_types=1);

namespace Spawnia\Sailor\Input\Operations\TakeLookup;

/**
 * @property string $__typename
 * @property int|null $takeLookup
 */
class TakeLookup extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|null $takeLookup
     */
    public static function make(
        $takeLookup = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        $instance->__typename = 'Mutation';
        if ($takeLookup !== self::UNDEFINED) {
            $instance->__set('takeLookup', $takeLookup);
        }

        return $instance;
    }

    protected function converters(): array
    {
        /** @var array<string, \Spawnia\Sailor\Convert\TypeConverter>|null $converters */
        static $converters;

        return $converters ??= [
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'takeLookup' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
        ];
    }

    public static function endpoint(): string
    {
        return 'input';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../sailor.php');
    }
}
