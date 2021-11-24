<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\AllMembers\Members;

class User extends \Spawnia\Sailor\TypedObject
{
    /** @var string|null */
    public $name;

    public function nameTypeMapper(): callable
    {
        return new \Spawnia\Sailor\Mapper\DirectMapper();
    }
}
