<?php declare(strict_types=1);

namespace Spawnia\Sailor\Convert;

use Spawnia\Sailor\ObjectLike;
use stdClass;

/**
 * @phpstan-type PolymorphicMapping array<string, class-string<ObjectLike>>
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

    public function fromGraphQL($value): ObjectLike
    {
        if (! $value instanceof stdClass) {
            throw new \InvalidArgumentException('Expected stdClass, got: ' . gettype($value));
        }

        return $this->mapping[$value->__typename]::fromStdClass($value);
    }

    public function toGraphQL($value)
    {
        if (! $value instanceof ObjectLike) {
            throw new \Exception('Should never happen');
        }

        return $value->toStdClass();
    }
}
