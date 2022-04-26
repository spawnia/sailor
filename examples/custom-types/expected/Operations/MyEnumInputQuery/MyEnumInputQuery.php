<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations\MyEnumInputQuery;

/**
 * @property string $__typename
 * @property \Spawnia\Sailor\CustomTypes\Operations\MyEnumInputQuery\WithEnumInput\EnumObject|null $withEnumInput
 */
class MyEnumInputQuery extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \Spawnia\Sailor\CustomTypes\Operations\MyEnumInputQuery\WithEnumInput\EnumObject|null $withEnumInput
     */
    public static function make($withEnumInput = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.'): self
    {
        $instance = new self;

        $instance->__typename = 'Query';
        if ($withEnumInput !== self::UNDEFINED) {
            $instance->withEnumInput = $withEnumInput;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'withEnumInput' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\CustomTypes\Operations\MyEnumInputQuery\WithEnumInput\EnumObject),
        ];
    }

    public static function endpoint(): string
    {
        return 'custom-types';
    }

    public static function config(): string
    {
        return __DIR__ . '/../../../sailor.php';
    }
}
