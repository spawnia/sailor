<?php declare(strict_types=1);

namespace Spawnia\Sailor\InlineFragments\Operations\SearchQuery;

class SearchQueryErrorFreeResult extends \Spawnia\Sailor\ErrorFreeResult
{
    public SearchQuery $data;

    public static function endpoint(): string
    {
        return 'inline-fragments';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../sailor.php');
    }
}
