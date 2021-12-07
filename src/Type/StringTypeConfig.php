<?php declare(strict_types=1);

namespace Spawnia\Sailor\Type;

use Spawnia\Sailor\TypeConverter\StringConverter;

class StringTypeConfig implements TypeConfig
{
    public function typeConverter(): string
    {
        return StringConverter::class;
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
