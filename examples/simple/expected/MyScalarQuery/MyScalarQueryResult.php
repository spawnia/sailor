<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\MyScalarQuery;

class MyScalarQueryResult extends \Spawnia\Sailor\Result
{
    /** @var MyScalarQuery|null */
    public $data;

    protected function setData(\stdClass $data): void
    {
        $this->data = MyScalarQuery::fromStdClass($data);
    }
}
