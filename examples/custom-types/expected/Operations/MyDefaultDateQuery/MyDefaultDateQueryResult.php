<?php

declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations\MyDefaultDateQuery;

class MyDefaultDateQueryResult extends \Spawnia\Sailor\Result
{
    public ?MyDefaultDateQuery $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = MyDefaultDateQuery::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(MyDefaultDateQuery $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): MyDefaultDateQueryErrorFreeResult
    {
        return MyDefaultDateQueryErrorFreeResult::fromResult($this);
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
