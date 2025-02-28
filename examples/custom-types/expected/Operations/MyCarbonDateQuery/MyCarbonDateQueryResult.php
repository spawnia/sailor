<?php declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\Operations\MyCarbonDateQuery;

class MyCarbonDateQueryResult extends \Spawnia\Sailor\Result
{
    public ?MyCarbonDateQuery $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = MyCarbonDateQuery::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(MyCarbonDateQuery $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): MyCarbonDateQueryErrorFreeResult
    {
        return MyCarbonDateQueryErrorFreeResult::fromResult($this);
    }

    public static function endpoint(): string
    {
        return 'custom-types';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../sailor.php');
    }
}
