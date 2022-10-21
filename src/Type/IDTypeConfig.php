<?php declare(strict_types=1);

namespace Spawnia\Sailor\Type;

use Spawnia\Sailor\Convert\IDConverter;

class IDTypeConfig implements TypeConfig, InputTypeConfig, OutputTypeConfig
{
    public function typeConverter(): string
    {
        return IDConverter::class;
    }

    public function inputTypeReference(): string
    {
        return 'int|string';
    }

    public function outputTypeReference(): string
    {
        return 'string';
    }

    public function generateClasses(): iterable
    {
        return [];
    }
}
