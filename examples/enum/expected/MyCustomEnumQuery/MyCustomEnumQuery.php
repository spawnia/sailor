<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Enum\MyCustomEnumQuery;

class MyCustomEnumQuery extends \Spawnia\Sailor\TypedObject
{
    /** @var \Spawnia\Sailor\Enum\Enums\CustomEnum|null */
    public $withCustomEnum;

    public function withCustomEnumTypeMapper(): \Spawnia\Sailor\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\TypeConverter\NullConverter(new \Spawnia\Sailor\Enum\TypeConverters\CustomEnum);
    }
}
