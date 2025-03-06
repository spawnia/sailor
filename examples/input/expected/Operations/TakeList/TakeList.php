<?php declare(strict_types=1);

namespace Spawnia\Sailor\Input\Operations\TakeList;

/**
 * @property string $__typename
 * @property array<int, int|null>|null $takeList
 */
class TakeList extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param array<int, int|null>|null $takeList
     */
    public static function make(
        $takeList = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        $instance->__typename = 'Mutation';
        if ($takeList !== self::UNDEFINED) {
            $instance->takeList = $takeList;
        }

        return $instance;
    }

    protected function converters(): array
    {
        /** @var array<string, \Spawnia\Sailor\Convert\TypeConverter>|null $converters */
        static $converters;

        return $converters ??= [
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'takeList' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter))),
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
