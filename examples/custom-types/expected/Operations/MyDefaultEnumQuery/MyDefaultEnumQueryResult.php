<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations\MyDefaultEnumQuery;

class MyDefaultEnumQueryResult extends \Spawnia\Sailor\Result
{
    public ?MyDefaultEnumQuery $data = null;

    public static function config(): string
    {
        return '/home/bfranke/projects/sailor/tests/Integration/../../examples/custom-types/sailor.php';
    }

    public static function endpoint(): string
    {
        return 'custom-types';
    }

    protected function setData(\stdClass $data): void
    {
        $this->data = MyDefaultEnumQuery::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(MyDefaultEnumQuery $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): MyDefaultEnumQueryErrorFreeResult
    {
        return MyDefaultEnumQueryErrorFreeResult::fromResult($this);
    }
}
