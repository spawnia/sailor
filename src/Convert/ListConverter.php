<?php declare(strict_types=1);

namespace Spawnia\Sailor\Convert;

class ListConverter implements TypeConverter
{
    protected TypeConverter $ofType;

    public function __construct(TypeConverter $ofType)
    {
        $this->ofType = $ofType;
    }

    /** @return array<int, mixed> */
    public function fromGraphQL($value): array
    {
        if (! is_array($value)) {
            $notArray = gettype($value);
            throw new \InvalidArgumentException("Expected array, got {$notArray}");
        }

        // @phpstan-ignore-next-line Parameter #1 $callback of function array_map expects (callable(mixed): mixed)|null, array{Spawnia\Sailor\TypeConverter, 'fromGraphQL'} given.
        return array_map([$this->ofType, 'fromGraphQL'], $value);
    }

    /** @return array<int, mixed> */
    public function toGraphQL($value): array
    {
        if (! is_array($value)) {
            $notArray = gettype($value);
            throw new \InvalidArgumentException("Expected array, got {$notArray}");
        }

        $graphQLValues = array_map([$this->ofType, 'toGraphQL'], $value);

        // Accept any incoming array as if it were a proper list
        return array_values($graphQLValues);
    }
}
