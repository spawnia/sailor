<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations\AllMembers;

class AllMembersResult extends \Spawnia\Sailor\Result
{
    public ?AllMembers $data;

    protected function setData(\stdClass $data): void
    {
        $this->data = AllMembers::fromStdClass($data);
    }

    public function errorFree(): AllMembersErrorFreeResult
    {
        return AllMembersErrorFreeResult::fromResult($this);
    }
}
