<?php declare(strict_types=1);

namespace Spawnia\Sailor\TypeConverter;

use Spawnia\Sailor\TypeConverter;

class ListConverter implements TypeConverter
{
    protected TypeConverter $ofType;

    public function __construct(TypeConverter $ofType)
    {
        $this->ofType = $ofType;
    }

    /**
     * @return array<int, mixed>
     */
    public function fromGraphQL($value): array
    {
        if (! is_array($value)) {
            throw new \InvalidArgumentException('Expected array, got ' . gettype($value));
        }

        // @phpstan-ignore-next-line Parameter #1 $callback of function array_map expects (callable(mixed): mixed)|null, array{Spawnia\Sailor\TypeConverter, 'fromGraphQL'} given.
        return array_map([$this->ofType, 'fromGraphQL'], $value);
    }

    /**
     * @return array<int, mixed>
     */
    public function toGraphQL($value): array
    {
        if (! is_array($value)) {
            throw new \InvalidArgumentException('Expected array, got ' . gettype($value));
        }

        return array_map([$this->ofType, 'toGraphQL'], $value);
    }
}
