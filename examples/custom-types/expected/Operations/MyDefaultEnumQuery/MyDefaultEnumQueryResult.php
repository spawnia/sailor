<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations\MyDefaultEnumQuery;

class MyDefaultEnumQueryResult extends \Spawnia\Sailor\Result
{
    public ?MyDefaultEnumQuery $data = null;

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

    public static function endpoint(): string
    {
        return 'custom-types';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../sailor.php');
    }
}
