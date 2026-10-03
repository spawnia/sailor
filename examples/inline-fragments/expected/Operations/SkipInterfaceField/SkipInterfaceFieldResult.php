<?php declare(strict_types=1);

namespace Spawnia\Sailor\InlineFragments\Operations\SkipInterfaceField;

class SkipInterfaceFieldResult extends \Spawnia\Sailor\Result
{
    public ?SkipInterfaceField $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = SkipInterfaceField::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(SkipInterfaceField $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): SkipInterfaceFieldErrorFreeResult
    {
        return SkipInterfaceFieldErrorFreeResult::fromResult($this);
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
