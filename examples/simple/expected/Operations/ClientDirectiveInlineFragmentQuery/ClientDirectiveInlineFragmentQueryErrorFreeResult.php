<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\ClientDirectiveInlineFragmentQuery;

class ClientDirectiveInlineFragmentQueryErrorFreeResult extends \Spawnia\Sailor\ErrorFreeResult
{
    public ClientDirectiveInlineFragmentQuery $data;

    public static function endpoint(): string
    {
        return 'simple';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../sailor.php');
    }
}
