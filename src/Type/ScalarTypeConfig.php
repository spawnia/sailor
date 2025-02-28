<?php declare(strict_types=1);

namespace Spawnia\Sailor\Type;

use Spawnia\Sailor\Convert\ScalarConverter;

class ScalarTypeConfig implements TypeConfig, InputTypeConfig, OutputTypeConfig
{
    public function typeConverter(): string
    {
        return ScalarConverter::class;
    }

    protected function typeReference(): string
    {
        // While typically serialized as a string, custom scalars may use other data types.
        // See https://spec.graphql.org/draft/#sec-Scalars.Custom-Scalars.
        return 'mixed';
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
