<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\Introspection;

class IntrospectionResult extends \Spawnia\Sailor\Result
{
    public ?Introspection $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = Introspection::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(Introspection $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): IntrospectionErrorFreeResult
    {
        return IntrospectionErrorFreeResult::fromResult($this);
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
