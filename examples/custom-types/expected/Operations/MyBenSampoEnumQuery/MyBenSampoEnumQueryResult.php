<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations\MyBenSampoEnumQuery;

class MyBenSampoEnumQueryResult extends \Spawnia\Sailor\Result
{
    public ?MyBenSampoEnumQuery $data = null;

    public static function config(): string
    {
        return '/home/bfranke/projects/sailor/examples/custom-types/sailor.php';
    }

    public static function endpoint(): string
    {
        return 'custom-types';
    }

    protected function setData(\stdClass $data): void
    {
        $this->data = MyBenSampoEnumQuery::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(MyBenSampoEnumQuery $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): MyBenSampoEnumQueryErrorFreeResult
    {
        return MyBenSampoEnumQueryErrorFreeResult::fromResult($this);
    }
}
