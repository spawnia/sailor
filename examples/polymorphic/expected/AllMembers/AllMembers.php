<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\AllMembers;

class AllMembers extends \Spawnia\Sailor\TypedObject
{
    /** @var array<int, \Spawnia\Sailor\Polymorphic\AllMembers\Members\User|\Spawnia\Sailor\Polymorphic\AllMembers\Members\Organization> */
    public $members;

    public function membersTypeMapper(): callable
    {
        return new \Spawnia\Sailor\Mapper\PolymorphicMapper([
            'User' => '\\Spawnia\\Sailor\\Polymorphic\\AllMembers\\Members\\User',
            'Organization' => '\\Spawnia\\Sailor\\Polymorphic\\AllMembers\\Members\\Organization',
        ]);
    }
}
