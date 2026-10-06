<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\Introspection;

class IntrospectionErrorFreeResult extends \Spawnia\Sailor\ErrorFreeResult
{
    public Introspection $data;

    public static function endpoint(): string
    {
        return 'simple';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../sailor.php');
    }
}
