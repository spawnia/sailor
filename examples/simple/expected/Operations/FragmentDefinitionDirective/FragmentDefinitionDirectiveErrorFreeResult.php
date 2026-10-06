<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\FragmentDefinitionDirective;

class FragmentDefinitionDirectiveErrorFreeResult extends \Spawnia\Sailor\ErrorFreeResult
{
    public FragmentDefinitionDirective $data;

    public static function endpoint(): string
    {
        return 'simple';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../sailor.php');
    }
}
