<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations\MyNestedCustomObjectQuery\WithNestedCustomObject;

/**
 * @property string $__typename
 * @property \Spawnia\Sailor\CustomTypesSrc\CustomObject|null $bar
 * @property \Spawnia\Sailor\CustomTypesSrc\CustomObject|null $baz
 */
class NestedCustomObject extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \Spawnia\Sailor\CustomTypesSrc\CustomObject|null $bar
     * @param \Spawnia\Sailor\CustomTypesSrc\CustomObject|null $baz
     */
    public static function make(
        $bar = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $baz = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        $instance->__typename = 'NestedCustomObject';
        if ($bar !== self::UNDEFINED) {
            $instance->bar = $bar;
        }
        if ($baz !== self::UNDEFINED) {
            $instance->baz = $baz;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'bar' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\CustomTypes\TypeConverters\CustomOutputConverter),
            'baz' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\CustomTypes\TypeConverters\CustomOutputConverter),
        ];
    }

    public static function endpoint(): string
    {
        return 'custom-types';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../sailor.php');
    }
}
