<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\NodeMembers;

class NodeMembers extends \Spawnia\Sailor\TypedObject
{
    /** @var array<int, \Spawnia\Sailor\Polymorphic\NodeMembers\Members\User|\Spawnia\Sailor\Polymorphic\NodeMembers\Members\Organization> */
    public $members;

    public function membersTypeMapper(): callable
    {
        return new \Spawnia\Sailor\Mapper\PolymorphicMapper([
            'User' => '\\Spawnia\\Sailor\\Polymorphic\\NodeMembers\\Members\\User',
            'Organization' => '\\Spawnia\\Sailor\\Polymorphic\\NodeMembers\\Members\\Organization',
        ]);
    }
}
