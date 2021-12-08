<?php declare(strict_types=1);

namespace Spawnia\Sailor\Type;

use Spawnia\Sailor\Convert\ScalarConverter;

class ScalarTypeConfig implements TypeConfig
{
    public function typeConverter(): string
    {
        return ScalarConverter::class;
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
