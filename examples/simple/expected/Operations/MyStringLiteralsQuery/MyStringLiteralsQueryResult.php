<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\MyStringLiteralsQuery;

class MyStringLiteralsQueryResult extends \Spawnia\Sailor\Result
{
    public ?MyStringLiteralsQuery $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = MyStringLiteralsQuery::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(MyStringLiteralsQuery $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): MyStringLiteralsQueryErrorFreeResult
    {
        return MyStringLiteralsQueryErrorFreeResult::fromResult($this);
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
