<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\AllMembers\Members;

class User extends \Spawnia\Sailor\TypedObject
{
    /** @var string|null */
    public $name;

    public function nameTypeMapper(): \Spawnia\Sailor\TypeConverter
    {
        static $converter;
        return $converter ??= new \Spawnia\Sailor\TypeConverter\NullConverter(new \Spawnia\Sailor\TypeConverter\StringConverter);
    }
}
