<?php declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\UserOrPost;

class UserOrPostResult extends \Spawnia\Sailor\Result
{
    public ?UserOrPost $data;

    protected function setData(\stdClass $data): void
    {
        $this->data = UserOrPost::fromStdClass($data);
    }

    public function errorFree(): UserOrPostErrorFreeResult
    {
        return UserOrPostErrorFreeResult::fromResult($this);
    }
}
