<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations\MyBenSampoEnumQuery;

class MyBenSampoEnumQueryResult extends \Spawnia\Sailor\Result
{
    public ?MyBenSampoEnumQuery $data;

    protected function setData(\stdClass $data): void
    {
        $this->data = MyBenSampoEnumQuery::fromStdClass($data);
    }

    public function errorFree(): MyBenSampoEnumQueryErrorFreeResult
    {
        return MyBenSampoEnumQueryErrorFreeResult::fromResult($this);
    }
}
