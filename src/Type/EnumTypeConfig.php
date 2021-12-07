<?php declare(strict_types=1);

namespace Spawnia\Sailor\Type;

use Spawnia\Sailor\TypeConverter\EnumConverter;

class EnumTypeConfig implements TypeConfig
{
    public function typeConverter(): string
    {
        return EnumConverter::class;
    }

    public function typeReference(): string
    {
        return 'string';
    }

    public function generate(): iterable
    {
        return [];
    }
}
