<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\TwoArgs;

class TwoArgs extends \Spawnia\Sailor\TypedObject
{
    /** @var string|null */
    public $twoArgs;

    public function typeTwoArgs(): callable
    {
        return new \Spawnia\Sailor\Mapper\DirectMapper();
    }
}
