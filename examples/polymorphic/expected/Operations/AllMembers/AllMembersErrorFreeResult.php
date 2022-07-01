<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations\AllMembers;

class AllMembersErrorFreeResult extends \Spawnia\Sailor\ErrorFreeResult
{
    public AllMembers $data;

    public static function endpoint(): string
    {
        return 'polymorphic';
    }

    public static function config(): string
    {
        return __DIR__ . '/../../../sailor.php';
    }
}
