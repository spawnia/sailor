<?php declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations\MyNativeEnumQuery;

class MyNativeEnumQueryResult extends \Spawnia\Sailor\Result
{
    public ?MyNativeEnumQuery $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = MyNativeEnumQuery::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(MyNativeEnumQuery $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): MyNativeEnumQueryErrorFreeResult
    {
        return MyNativeEnumQueryErrorFreeResult::fromResult($this);
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
