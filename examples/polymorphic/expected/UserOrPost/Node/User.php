<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\UserOrPost\Node;

class User extends \Spawnia\Sailor\TypedObject
{
    /** @var string */
    public $id;

    /** @var string */
    public $name;

    public function idTypeMapper(): callable
    {
        return new \Spawnia\Sailor\Mapper\DirectMapper();
    }

    public function nameTypeMapper(): callable
    {
        return new \Spawnia\Sailor\Mapper\DirectMapper();
    }
}
