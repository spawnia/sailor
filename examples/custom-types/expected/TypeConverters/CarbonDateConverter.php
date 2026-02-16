<?php declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\TypeConverters;

class CarbonDateConverter implements \Spawnia\Sailor\Convert\TypeConverter
{
    public function fromGraphQL($value): \Carbon\Carbon
    {
        if (! is_string($value)) {
            throw new \InvalidArgumentException('Expected string, got: '.gettype($value));
        }

        $date = \Carbon\Carbon::createFromFormat('Y-m-d', $value);
        if (! $date) { // @phpstan-ignore-line avoiding strict comparison, as different Carbon versions may return null or false
            throw new \InvalidArgumentException("Expected date with format Y-m-d, got {$value}.");
        }

        return $date;
    }

    public function toGraphQL($value)
    {
        if (! $value instanceof \Carbon\Carbon) {
            $actualType = gettype($value);
            throw new \InvalidArgumentException("Expected instanceof Carbon\Carbon, got {$actualType}.");
        }

        return $value->format('Y-m-d');
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
