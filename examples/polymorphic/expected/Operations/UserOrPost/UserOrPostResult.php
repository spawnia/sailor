<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations\UserOrPost;

class UserOrPostResult extends \Spawnia\Sailor\Result
{
    public ?UserOrPost $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = UserOrPost::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(UserOrPost $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): UserOrPostErrorFreeResult
    {
        return UserOrPostErrorFreeResult::fromResult($this);
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
