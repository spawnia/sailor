<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations\AllMembers;

class AllMembersResult extends \Spawnia\Sailor\Result
{
    public ?AllMembers $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = AllMembers::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(AllMembers $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): AllMembersErrorFreeResult
    {
        return AllMembersErrorFreeResult::fromResult($this);
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
