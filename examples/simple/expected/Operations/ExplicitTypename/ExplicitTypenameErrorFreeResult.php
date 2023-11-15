<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\ExplicitTypename;

class ExplicitTypenameErrorFreeResult extends \Spawnia\Sailor\ErrorFreeResult
{
    public ExplicitTypename $data;

    public static function endpoint(): string
    {
        return 'simple';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../sailor.php');
    }
}
