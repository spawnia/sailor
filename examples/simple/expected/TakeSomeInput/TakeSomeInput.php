<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\TakeSomeInput;

class TakeSomeInput extends \Spawnia\Sailor\TypedObject
{
    /** @var int|null */
    public $takeSomeInput;

    public function takeSomeInputTypeMapper(): callable
    {
        return new \Spawnia\Sailor\Mapper\DirectMapper();
    }
}
