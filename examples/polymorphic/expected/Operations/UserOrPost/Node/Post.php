<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations\UserOrPost\Node;

class Post extends \Spawnia\Sailor\TypedObject
{
    /** @var string */
    public $__typename;

    /** @var string */
    public $id;

    /** @var string|null */
    public $title;

    public function __typenameTypeMapper(): \Spawnia\Sailor\Convert\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter));
    }

    public function idTypeMapper(): \Spawnia\Sailor\Convert\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter));
    }

    public function titleTypeMapper(): \Spawnia\Sailor\Convert\TypeConverter
    {
        static $converter;

        return $converter ??= new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter);
    }
}
