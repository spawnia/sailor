<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\TypeConverters;

class CustomDateConverter implements \Spawnia\Sailor\Convert\TypeConverter
{
    public function fromGraphQL($value): \DateTime
    {
        if (! is_string($value)) {
            throw new \InvalidArgumentException('Expected string, got: '.gettype($value));
        }

        $date = \DateTime::createFromFormat('Y-m-d H:i:s', $value);
        if ($date === false) {
            throw new \InvalidArgumentException("Expected date with format Y-m-d H:i:s, got {$value}");
        }

        return $date;
    }

    public function toGraphQL($value)
    {
        if (! $value instanceof \DateTime) {
            throw new \InvalidArgumentException('Expected instanceof DateTime, got: '.gettype($value));
        }

        return $value->format('Y-m-d H:i:s');
    }

    public static function endpoint(): string
    {
        return 'custom-types';
    }

    public static function config(): string
    {
        return __DIR__ . '/../../sailor.php';
    }
}
