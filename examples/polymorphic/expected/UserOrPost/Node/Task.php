<?php

namespace Spawnia\Sailor\Polymorphic\UserOrPost\Node;

class Task extends \Spawnia\Sailor\TypedObject
{
    /** @var string */
    public $id;

    public function idTypeMapper(): callable
    {
        return new \Spawnia\Sailor\Mapper\DirectMapper();
    }
}
