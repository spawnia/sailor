<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations\NodeMembers;

class NodeMembersErrorFreeResult extends \Spawnia\Sailor\ErrorFreeResult
{
    public NodeMembers $data;

    public static function endpoint(): string
    {
        return 'polymorphic';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../sailor.php');
    }
}
