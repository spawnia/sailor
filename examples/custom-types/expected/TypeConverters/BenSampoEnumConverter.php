<?php declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\TypeConverters;

class BenSampoEnumConverter implements \Spawnia\Sailor\Convert\TypeConverter
{
    public function fromGraphQL($value): \Spawnia\Sailor\CustomTypes\Types\BenSampoEnum
    {
        return new \Spawnia\Sailor\CustomTypes\Types\BenSampoEnum($value);
    }

    public function toGraphQL($value): string
    {
        if (! $value instanceof \Spawnia\Sailor\CustomTypes\Types\BenSampoEnum) {
            $actualType = gettype($value);
            throw new \InvalidArgumentException("Expected instanceof Spawnia\Sailor\CustomTypes\Types\BenSampoEnum, got {$actualType}.");
        }

        // @phpstan-ignore-next-line generated enum values are always strings
        return $value->value;
    }

    public static function endpoint(): string
    {
        return 'custom-types';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../sailor.php');
    }
}
