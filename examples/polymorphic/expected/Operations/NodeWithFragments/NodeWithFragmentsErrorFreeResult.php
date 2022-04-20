<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments;

class NodeWithFragmentsErrorFreeResult extends \Spawnia\Sailor\ErrorFreeResult
{
    public NodeWithFragments $data;

    public static function config(): string
    {
        return '/home/bfranke/projects/sailor/examples/polymorphic/sailor.php';
    }

    public static function endpoint(): string
    {
        return 'polymorphic';
    }
}
