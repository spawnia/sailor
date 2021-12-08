<?php declare(strict_types=1);

namespace Spawnia\Sailor\Type;

use Spawnia\Sailor\Convert\IDConverter;

class IDTypeConfig implements TypeConfig
{
    public function typeConverter(): string
    {
        return IDConverter::class;
    }

    public function typeReference(): string
    {
        return 'string';
    }

    public function generateClasses(): iterable
    {
        return [];
    }
}
