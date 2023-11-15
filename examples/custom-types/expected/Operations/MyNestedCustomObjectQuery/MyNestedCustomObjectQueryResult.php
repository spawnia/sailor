<?php declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations\MyNestedCustomObjectQuery;

class MyNestedCustomObjectQueryResult extends \Spawnia\Sailor\Result
{
    public ?MyNestedCustomObjectQuery $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = MyNestedCustomObjectQuery::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(MyNestedCustomObjectQuery $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): MyNestedCustomObjectQueryErrorFreeResult
    {
        return MyNestedCustomObjectQueryErrorFreeResult::fromResult($this);
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
