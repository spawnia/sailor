<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Enum\MyDefaultEnumQuery;

class MyDefaultEnumQuery extends \Spawnia\Sailor\TypedObject
{
    /** @var string */
    public $withDefaultEnum;

    public function withDefaultEnumTypeMapper(): \Spawnia\Sailor\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\TypeConverter\NullConverter(new \Spawnia\Sailor\TypeConverter\NonNullConverter(new \Spawnia\Sailor\TypeConverter\EnumConverter));
    }
}
