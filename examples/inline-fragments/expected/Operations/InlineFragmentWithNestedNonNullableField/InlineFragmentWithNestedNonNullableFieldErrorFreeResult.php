<?php declare(strict_types=1);

namespace Spawnia\Sailor\InlineFragments\Operations\InlineFragmentWithNestedNonNullableField;

class InlineFragmentWithNestedNonNullableFieldErrorFreeResult extends \Spawnia\Sailor\ErrorFreeResult
{
    public InlineFragmentWithNestedNonNullableField $data;

    public static function endpoint(): string
    {
        return 'inline-fragments';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../sailor.php');
    }
}
