<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Inputs;

class SomeInput
{
    /** @var string */
    public $id;

    /** @var string|null */
    public $name;

    /** @var string|null */
    public $value;

    /** @var array<int, array<int, int|null>> */
    public $matrix;

    /** @var \Spawnia\Sailor\Simple\Inputs\SomeInput|null */
    public $nested;
}
