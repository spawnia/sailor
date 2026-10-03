<?php declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\Type;

class PropertyDefinition
{
    public string $name;

    public Type $type;

    public string $phpDocType;

    public string $typeConverter;

    /** @var mixed any value */
    public $defaultValue;

    public bool $isOmittable;

    /** @param mixed $defaultValue any value */
    public function __construct(string $name, Type $type, string $phpDocType, string $typeConverter, $defaultValue, bool $isOmittable)
    {
        $this->name = $name;
        $this->type = $type;
        $this->phpDocType = $phpDocType;
        $this->typeConverter = $typeConverter;
        $this->defaultValue = $defaultValue;
        $this->isOmittable = $isOmittable;
    }

    /** Omitted fields are null in the result, but present ones keep the non-null check of their converter. */
    public function resultType(): Type
    {
        return $this->isOmittable && $this->type instanceof NonNull
            ? $this->type->getWrappedType()
            : $this->type;
    }

    public function isRequired(): bool
    {
        return $this->resultType() instanceof NonNull
            && $this->defaultValue === null;
    }
}
