<?php declare(strict_types=1);

namespace Spawnia\Sailor\Type;

use Spawnia\Sailor\Convert\FloatConverter;

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

    public function generateClasses(): iterable
    {
        return [];
    }
}
