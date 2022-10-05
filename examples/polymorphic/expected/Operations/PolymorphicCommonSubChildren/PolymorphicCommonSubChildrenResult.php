<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Polymorphic\Operations\PolymorphicCommonSubChildren;

class PolymorphicCommonSubChildrenResult extends \Spawnia\Sailor\Result
{
    public ?PolymorphicCommonSubChildren $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = PolymorphicCommonSubChildren::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(PolymorphicCommonSubChildren $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): PolymorphicCommonSubChildrenErrorFreeResult
    {
        return PolymorphicCommonSubChildrenErrorFreeResult::fromResult($this);
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
