<?php declare(strict_types=1);

namespace Spawnia\Sailor\PhpKeywords\Operations\_Catch;

class _CatchErrorFreeResult extends \Spawnia\Sailor\ErrorFreeResult
{
    public _Catch $data;

    public static function endpoint(): string
    {
        return 'php-keywords';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../sailor.php');
    }
}
