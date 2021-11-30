<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\UserOrPost\Node;

class User extends \Spawnia\Sailor\TypedObject
{
    /** @var string */
    public $id;

    /** @var string|null */
    public $name;

    public function idTypeMapper(): \Spawnia\Sailor\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\TypeConverter\NullConverter(new \Spawnia\Sailor\TypeConverter\NonNullConverter(new \Spawnia\Sailor\TypeConverter\IDConverter));
    }

    public function nameTypeMapper(): \Spawnia\Sailor\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\TypeConverter\NullConverter(new \Spawnia\Sailor\TypeConverter\StringConverter);
    }
}
