<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations\NodeWithFragments;

class NodeWithFragmentsResult extends \Spawnia\Sailor\Result
{
    public ?NodeWithFragments $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = NodeWithFragments::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(NodeWithFragments $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): NodeWithFragmentsErrorFreeResult
    {
        return NodeWithFragmentsErrorFreeResult::fromResult($this);
    }

    public static function endpoint(): string
    {
        return 'polymorphic';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../sailor.php');
    }
}
