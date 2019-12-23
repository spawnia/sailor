<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\MyObjectQuery;

class MyObjectQueryResult extends \Spawnia\Sailor\Result
{
    /** @var MyObjectQuery|null */
    public $data;

    protected function setData(\stdClass $data): void
    {
        $this->data = MyObjectQuery::fromStdClass($data);
    }
}
