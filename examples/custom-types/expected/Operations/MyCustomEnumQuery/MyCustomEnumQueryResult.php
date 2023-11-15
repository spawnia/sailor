<?php declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations\MyCustomEnumQuery;

class MyCustomEnumQueryResult extends \Spawnia\Sailor\Result
{
    public ?MyCustomEnumQuery $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = MyCustomEnumQuery::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(MyCustomEnumQuery $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): MyCustomEnumQueryErrorFreeResult
    {
        return MyCustomEnumQueryErrorFreeResult::fromResult($this);
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
