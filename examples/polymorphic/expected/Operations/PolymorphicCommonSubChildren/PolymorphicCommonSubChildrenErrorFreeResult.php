<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations\PolymorphicCommonSubChildren;

class PolymorphicCommonSubChildrenErrorFreeResult extends \Spawnia\Sailor\ErrorFreeResult
{
    public PolymorphicCommonSubChildren $data;

    public static function endpoint(): string
    {
        return 'polymorphic';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../sailor.php');
    }
}
