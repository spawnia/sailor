<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\NestedWithFragments;

class NestedWithFragmentsResult extends \Spawnia\Sailor\Result
{
    public ?NestedWithFragments $data = null;

    public static function config(): string
    {
        return '/home/bfranke/projects/sailor/examples/simple/sailor.php';
    }

    public static function endpoint(): string
    {
        return 'simple';
    }

    protected function setData(\stdClass $data): void
    {
        $this->data = NestedWithFragments::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(NestedWithFragments $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): NestedWithFragmentsErrorFreeResult
    {
        return NestedWithFragmentsErrorFreeResult::fromResult($this);
    }
}
