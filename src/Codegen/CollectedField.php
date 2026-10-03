<?php declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Type\Definition\Type;

class CollectedField
{
    public string $responseName;

    public Type $type;

    /** @var array<int, array<string, true>> per occurrence, the @skip and @include conditions it is selected under */
    public array $conditions = [];

    public function __construct(string $responseName, Type $type)
    {
        $this->responseName = $responseName;
        $this->type = $type;
    }

    /** @param array<string, true> $conditions */
    public function isSelectedUnder(array $conditions): bool
    {
        foreach ($this->conditions as $occurrenceConditions) {
            if (array_diff_key($occurrenceConditions, $conditions) === []) {
                return true;
            }
        }

        return false;
    }
}
