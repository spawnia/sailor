<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\MyObjectQuery;

class MyObjectQueryResult extends \Spawnia\Sailor\Result
{
    public ?MyObjectQuery $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = MyObjectQuery::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(MyObjectQuery $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): MyObjectQueryErrorFreeResult
    {
        return MyObjectQueryErrorFreeResult::fromResult($this);
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
