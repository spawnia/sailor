<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\MyObjectQuery\SingleObject;

/**
 * @property string $__typename
 * @property int|null $value
 */
class SomeObject extends \Spawnia\Sailor\Type\TypedObject
{
    /** @var string */
    public $__typename;

    /** @var int|null */
    public $value;

    public function converters(): array
    {
        static $converters;

        return $converters ??= [
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'value' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
        ];
    }

    public function __typenameTypeMapper(): \Spawnia\Sailor\Convert\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter);
    }

    public function valueTypeMapper(): \Spawnia\Sailor\Convert\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter);
    }
}
