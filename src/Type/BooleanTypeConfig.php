<?php declare(strict_types=1);

namespace Spawnia\Sailor\Type;

use Spawnia\Sailor\Convert\BooleanConverter;

class BooleanTypeConfig implements TypeConfig
{
    public function typeConverter(): string
    {
        return BooleanConverter::class;
    }

    public function typeReference(): string
    {
        return 'bool';
    }

    public function generate(): iterable
    {
        return [];
    }
}
