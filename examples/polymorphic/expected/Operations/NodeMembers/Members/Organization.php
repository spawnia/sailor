<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations\NodeMembers\Members;

class Organization extends \Spawnia\Sailor\TypedObject
{
    /** @var string */
    public $__typename;

    public function __typenameTypeMapper(): \Spawnia\Sailor\Convert\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter));
    }
}
