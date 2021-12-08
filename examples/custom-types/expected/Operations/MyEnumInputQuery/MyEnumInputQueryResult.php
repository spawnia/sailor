<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations\MyEnumInputQuery;

class MyEnumInputQueryResult extends \Spawnia\Sailor\Result
{
    public ?MyEnumInputQuery $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = MyEnumInputQuery::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(MyEnumInputQuery $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): MyEnumInputQueryErrorFreeResult
    {
        return MyEnumInputQueryErrorFreeResult::fromResult($this);
    }
}
