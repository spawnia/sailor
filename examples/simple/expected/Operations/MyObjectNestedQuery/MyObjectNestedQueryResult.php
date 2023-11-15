<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\MyObjectNestedQuery;

class MyObjectNestedQueryResult extends \Spawnia\Sailor\Result
{
    public ?MyObjectNestedQuery $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = MyObjectNestedQuery::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(MyObjectNestedQuery $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): MyObjectNestedQueryErrorFreeResult
    {
        return MyObjectNestedQueryErrorFreeResult::fromResult($this);
    }

    public static function endpoint(): string
    {
        return 'simple';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../sailor.php');
    }
}
