<?php declare(strict_types=1);

namespace Spawnia\Sailor\Type;

use Spawnia\Sailor\Convert\BooleanConverter;

class BooleanTypeConfig implements TypeConfig, InputTypeConfig, OutputTypeConfig
{
    public function typeConverter(): string
    {
        return BooleanConverter::class;
    }

    protected function typeReference(): string
    {
        return 'bool';
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
