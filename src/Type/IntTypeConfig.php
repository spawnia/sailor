<?php declare(strict_types=1);

namespace Spawnia\Sailor\Type;

use Spawnia\Sailor\Convert\IntConverter;

class IntTypeConfig implements TypeConfig, InputTypeConfig, OutputTypeConfig
{
    public function typeConverter(): string
    {
        return IntConverter::class;
    }

    protected function typeReference(): string
    {
        return 'int';
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
