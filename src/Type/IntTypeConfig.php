<?php declare(strict_types=1);

namespace Spawnia\Sailor\Type;

use Spawnia\Sailor\TypeConverter\IntConverter;

class IntTypeConfig implements TypeConfig
{
    public function typeConverter(): string
    {
        return IntConverter::class;
    }

    public function typeReference(): string
    {
        return 'int';
    }

    public function generate(): iterable
    {
        return [];
    }
}
