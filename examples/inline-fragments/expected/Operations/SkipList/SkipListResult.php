<?php declare(strict_types=1);

namespace Spawnia\Sailor\InlineFragments\Operations\SkipList;

class SkipListResult extends \Spawnia\Sailor\Result
{
    public ?SkipList $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = SkipList::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(SkipList $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): SkipListErrorFreeResult
    {
        return SkipListErrorFreeResult::fromResult($this);
    }

    public static function endpoint(): string
    {
        return 'inline-fragments';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../sailor.php');
    }
}
