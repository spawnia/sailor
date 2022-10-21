<?php declare(strict_types=1);

namespace Spawnia\Sailor\Type;

use Spawnia\Sailor\Convert\StringConverter;

class StringTypeConfig implements TypeConfig, InputTypeConfig, OutputTypeConfig
{
    public function typeConverter(): string
    {
        return StringConverter::class;
    }

    protected function typeReference(): string
    {
        return 'string';
    }

    public function inputTypeReference(): string
    {
        return $this->typeReference();
    }

    public function outputTypeReference(): string
    {
        return $this->typeReference();
    }

    public function generateClasses(): iterable
    {
        return [];
    }
}
