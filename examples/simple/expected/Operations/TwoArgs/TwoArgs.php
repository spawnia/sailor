<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\TwoArgs;

class TwoArgs extends \Spawnia\Sailor\TypedObject
{
    /** @var string */
    public $__typename;

    /** @var string|null */
    public $twoArgs;

    public function __typenameTypeMapper(): \Spawnia\Sailor\Convert\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter);
    }

    public function twoArgsTypeMapper(): \Spawnia\Sailor\Convert\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter);
    }
}
