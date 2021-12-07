<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations\NodeMembers;

class NodeMembersResult extends \Spawnia\Sailor\Result
{
    public ?NodeMembers $data;

    protected function setData(\stdClass $data): void
    {
        $this->data = NodeMembers::fromStdClass($data);
    }

    public function errorFree(): NodeMembersErrorFreeResult
    {
        return NodeMembersErrorFreeResult::fromResult($this);
    }
}
