<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\ClientDirectiveQuery;

class ClientDirectiveQueryResult extends \Spawnia\Sailor\Result
{
    public ?ClientDirectiveQuery $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = ClientDirectiveQuery::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(ClientDirectiveQuery $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): ClientDirectiveQueryErrorFreeResult
    {
        return ClientDirectiveQueryErrorFreeResult::fromResult($this);
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
