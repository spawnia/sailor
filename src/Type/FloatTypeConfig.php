<?php declare(strict_types=1);

namespace Spawnia\Sailor\Type;

use Spawnia\Sailor\TypeConverter\FloatConverter;

class FloatTypeConfig implements TypeConfig
{
    public function typeConverter(): string
    {
        return FloatConverter::class;
    }

    public function typeReference(): string
    {
        return 'float';
    }

    public function generate(): iterable
    {
        return [];
    }
}
