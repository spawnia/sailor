<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\FragmentDefinitionDirective;

class FragmentDefinitionDirectiveResult extends \Spawnia\Sailor\Result
{
    public ?FragmentDefinitionDirective $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = FragmentDefinitionDirective::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(FragmentDefinitionDirective $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): FragmentDefinitionDirectiveErrorFreeResult
    {
        return FragmentDefinitionDirectiveErrorFreeResult::fromResult($this);
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
