<?php declare(strict_types=1);

namespace Spawnia\Sailor\Type;

use Spawnia\Sailor\Convert\FloatConverter;

class FloatTypeConfig implements TypeConfig, InputTypeConfig, OutputTypeConfig
{
    public function typeConverter(): string
    {
        return FloatConverter::class;
    }

    public function inputTypeReference(): string
    {
        return 'float|int';
    }

    public function outputTypeReference(): string
    {
        return 'float';
    }

    public function generateClasses(): iterable
    {
        return [];
    }
}
