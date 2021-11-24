<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\NodeMembers\Members;

class User extends \Spawnia\Sailor\TypedObject
{
    /** @var string */
    public $id;

    public function idTypeMapper(): callable
    {
        return new \Spawnia\Sailor\Mapper\DirectMapper();
    }
}
