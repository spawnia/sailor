<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\TakeSomeInput;

class TakeSomeInput extends \Spawnia\Sailor\TypedObject
{
    /** @var int|null */
    public $takeSomeInput;

    public function takeSomeInputTypeMapper(): \Spawnia\Sailor\TypeConverter
    {
        static $converter;
        return $converter ??= new \Spawnia\Sailor\TypeConverter\NullConverter(new \Spawnia\Sailor\TypeConverter\IntConverter);
    }
}
