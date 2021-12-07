<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\MyEnumInputQuery;

class MyEnumInputQueryResult extends \Spawnia\Sailor\Result
{
    public ?MyEnumInputQuery $data;

    protected function setData(\stdClass $data): void
    {
        $this->data = MyEnumInputQuery::fromStdClass($data);
    }

    public function errorFree(): MyEnumInputQueryErrorFreeResult
    {
        return MyEnumInputQueryErrorFreeResult::fromResult($this);
    }
}
