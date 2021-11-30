<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\MyScalarQuery;

class MyScalarQuery extends \Spawnia\Sailor\TypedObject
{
    /** @var string|null */
    public $scalarWithArg;

    public function scalarWithArgTypeMapper(): \Spawnia\Sailor\TypeConverter
    {
        static $converter;
        return $converter ??= new \Spawnia\Sailor\TypeConverter\NullConverter(new \Spawnia\Sailor\TypeConverter\IDConverter);
    }
}
