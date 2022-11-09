<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\ClientDirectiveInlineFragmentQuery;

class ClientDirectiveInlineFragmentQueryResult extends \Spawnia\Sailor\Result
{
    public ?ClientDirectiveInlineFragmentQuery $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = ClientDirectiveInlineFragmentQuery::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(ClientDirectiveInlineFragmentQuery $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): ClientDirectiveInlineFragmentQueryErrorFreeResult
    {
        return ClientDirectiveInlineFragmentQueryErrorFreeResult::fromResult($this);
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
