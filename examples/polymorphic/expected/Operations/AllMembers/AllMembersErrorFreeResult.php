<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations\AllMembers;

class AllMembersErrorFreeResult extends \Spawnia\Sailor\ErrorFreeResult
{
    public AllMembers $data;

    public static function config(): string
    {
        return '/home/bfranke/projects/sailor/examples/polymorphic/sailor.php';
    }

    public static function endpoint(): string
    {
        return 'polymorphic';
    }
}
