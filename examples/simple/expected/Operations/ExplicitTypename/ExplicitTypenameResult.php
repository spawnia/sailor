<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\ExplicitTypename;

class ExplicitTypenameResult extends \Spawnia\Sailor\Result
{
    public ?ExplicitTypename $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = ExplicitTypename::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(ExplicitTypename $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): ExplicitTypenameErrorFreeResult
    {
        return ExplicitTypenameErrorFreeResult::fromResult($this);
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
