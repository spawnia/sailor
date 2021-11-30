<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\TwoArgs;

class TwoArgs extends \Spawnia\Sailor\TypedObject
{
    /** @var string|null */
    public $twoArgs;

    public function twoArgsTypeMapper(): \Spawnia\Sailor\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\TypeConverter\NullConverter(new \Spawnia\Sailor\TypeConverter\IDConverter);
    }
}
