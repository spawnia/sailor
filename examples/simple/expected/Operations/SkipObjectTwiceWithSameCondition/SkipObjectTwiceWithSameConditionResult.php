<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\SkipObjectTwiceWithSameCondition;

class SkipObjectTwiceWithSameConditionResult extends \Spawnia\Sailor\Result
{
    public ?SkipObjectTwiceWithSameCondition $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = SkipObjectTwiceWithSameCondition::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(SkipObjectTwiceWithSameCondition $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): SkipObjectTwiceWithSameConditionErrorFreeResult
    {
        return SkipObjectTwiceWithSameConditionErrorFreeResult::fromResult($this);
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
