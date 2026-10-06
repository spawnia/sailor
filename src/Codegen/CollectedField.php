<?php declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Type\Definition\Type;

class CollectedField
{
    public string $responseName;

    public Type $type;

    public function __construct(string $responseName, Type $type)
    {
        $this->responseName = $responseName;
        $this->type = $type;
    }
}
