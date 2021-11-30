<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Enum\MyCustomEnumQuery;

class MyCustomEnumQueryResult extends \Spawnia\Sailor\Result
{
    public ?MyCustomEnumQuery $data;

    protected function setData(\stdClass $data): void
    {
        $this->data = MyCustomEnumQuery::fromStdClass($data);
    }

    public function errorFree(): MyCustomEnumQueryErrorFreeResult
    {
        return MyCustomEnumQueryErrorFreeResult::fromResult($this);
    }
}
