<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\AllMembers\Members;

class Organization extends \Spawnia\Sailor\TypedObject
{
    /** @var string */
    public $code;

    public function codeTypeMapper(): callable
    {
        return new \Spawnia\Sailor\Mapper\DirectMapper();
    }
}
