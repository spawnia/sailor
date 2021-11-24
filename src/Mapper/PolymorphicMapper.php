<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Mapper;

use Spawnia\Sailor\TypedObject;
use Spawnia\Sailor\TypeMapper;

/**
 * @phpstan-type PolymorphicMapping array<string, class-string<TypedObject>>
 */
class PolymorphicMapper implements TypeMapper
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

    /**
     * @param  \stdClass  $value  An object representing an polymorphic type
     */
    // @phpstan-ignore-next-line contravariance is technically broken here
    public function __invoke($value): TypedObject
    {
        return $this->mapping[$value->__typename]::fromStdClass($value);
    }
}
