<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\NodeMembers\Members;

class Organization extends \Spawnia\Sailor\TypedObject
{
    /** @var string */
    public $__typename;

    public function __typenameTypeMapper(): \Spawnia\Sailor\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\TypeConverter\NullConverter(new \Spawnia\Sailor\TypeConverter\NonNullConverter(new \Spawnia\Sailor\TypeConverter\StringConverter));
    }
}
