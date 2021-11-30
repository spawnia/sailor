<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Enum\MyDefaultEnumQuery;

class MyDefaultEnumQueryResult extends \Spawnia\Sailor\Result
{
    public ?MyDefaultEnumQuery $data;

    protected function setData(\stdClass $data): void
    {
        $this->data = MyDefaultEnumQuery::fromStdClass($data);
    }

    public function errorFree(): MyDefaultEnumQueryErrorFreeResult
    {
        return MyDefaultEnumQueryErrorFreeResult::fromResult($this);
    }
}
