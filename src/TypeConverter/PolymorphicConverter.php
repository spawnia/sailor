<?php

declare(strict_types=1);

namespace Spawnia\Sailor\TypeConverter;

use Spawnia\Sailor\TypeConverter;
use Spawnia\Sailor\TypedObject;
use stdClass;

/**
 * @phpstan-type PolymorphicMapping array<string, class-string<TypedObject>>
 */
class PolymorphicConverter implements TypeConverter
{
    /**
     * @var PolymorphicMapping
     */
    protected array $mapping;

    /**
     * @param  PolymorphicMapping  $mapping
     */
    public function __construct(array $mapping)
    {
        $this->mapping = $mapping;
    }

    public function fromGraphQL($value): TypedObject
    {
        if (! $value instanceof stdClass) {
            throw new \InvalidArgumentException('Expected stdClass, got: ' . gettype($value));
        }

        return $this->mapping[$value->__typename]::fromStdClass($value);
    }

    public function toGraphQL($value)
    {
        throw new \Exception('Should never happen');
    }
}
