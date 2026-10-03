<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\SelectThenSkipNonNullable;

class SelectThenSkipNonNullableErrorFreeResult extends \Spawnia\Sailor\ErrorFreeResult
{
    public SelectThenSkipNonNullable $data;

    public static function endpoint(): string
    {
        return 'simple';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../sailor.php');
    }
}
