<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\NodeMembers\Members;

class User extends \Spawnia\Sailor\TypedObject
{
    /** @var string */
    public $id;

    public function idTypeMapper(): \Spawnia\Sailor\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\TypeConverter\NullConverter(new \Spawnia\Sailor\TypeConverter\NonNullConverter(new \Spawnia\Sailor\TypeConverter\IDConverter));
    }
}
