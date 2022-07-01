<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations\NodeMembers;

class NodeMembersResult extends \Spawnia\Sailor\Result
{
    public ?NodeMembers $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = NodeMembers::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(NodeMembers $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): NodeMembersErrorFreeResult
    {
        return NodeMembersErrorFreeResult::fromResult($this);
    }

    public static function endpoint(): string
    {
        return 'polymorphic';
    }

    public static function config(): string
    {
        return __DIR__ . '/../../../sailor.php';
    }
}
