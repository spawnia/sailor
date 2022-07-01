<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\TypeConverters;

class CustomEnumConverter implements \Spawnia\Sailor\Convert\TypeConverter
{
    public function fromGraphQL($value): \Spawnia\Sailor\CustomTypes\Types\CustomEnum
    {
        if (! is_string($value)) {
            throw new \InvalidArgumentException('Expected string, got: '.gettype($value));
        }

        return new \Spawnia\Sailor\CustomTypes\Types\CustomEnum($value);
    }

    public function toGraphQL($value): string
    {
        if (! $value instanceof \Spawnia\Sailor\CustomTypes\Types\CustomEnum) {
            throw new \InvalidArgumentException('Expected instanceof Enum, got: '.gettype($value));
        }

        return $value->value;
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
