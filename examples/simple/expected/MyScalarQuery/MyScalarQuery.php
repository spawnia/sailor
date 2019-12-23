<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\MyScalarQuery;

class MyScalarQuery extends \Spawnia\Sailor\TypedObject
{
    /** @var string|null */
    public $scalarWithArg;

    public function typeScalarWithArg(): callable
    {
        return new \Spawnia\Sailor\Mapper\DirectMapper();
    }
}
