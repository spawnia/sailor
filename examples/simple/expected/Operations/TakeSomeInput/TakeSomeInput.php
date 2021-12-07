<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\TakeSomeInput;

class TakeSomeInput extends \Spawnia\Sailor\TypedObject
{
    /** @var string */
    public $__typename;

    /** @var int|null */
    public $takeSomeInput;

    public function __typenameTypeMapper(): \Spawnia\Sailor\Convert\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter);
    }

    public function takeSomeInputTypeMapper(): \Spawnia\Sailor\Convert\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter);
    }
}
