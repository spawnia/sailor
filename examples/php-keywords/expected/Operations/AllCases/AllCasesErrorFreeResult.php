<?php declare(strict_types=1);

namespace Spawnia\Sailor\PhpKeywords\Operations\AllCases;

class AllCasesErrorFreeResult extends \Spawnia\Sailor\ErrorFreeResult
{
    public AllCases $data;

    public static function endpoint(): string
    {
        return 'php-keywords';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../sailor.php');
    }
}
