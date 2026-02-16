<?php declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations\MyCustomObjectQuery;

class MyCustomObjectQueryResult extends \Spawnia\Sailor\Result
{
    public ?MyCustomObjectQuery $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = MyCustomObjectQuery::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(MyCustomObjectQuery $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): MyCustomObjectQueryErrorFreeResult
    {
        return MyCustomObjectQueryErrorFreeResult::fromResult($this);
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
