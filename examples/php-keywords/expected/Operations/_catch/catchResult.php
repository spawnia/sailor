<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\_catch;

class catchResult extends \Spawnia\Sailor\Result
{
    public ?catch $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = catch::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(catch $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): catchErrorFreeResult
    {
        return catchErrorFreeResult::fromResult($this);
    }

    public static function endpoint(): string
    {
        return 'php-keywords';
    }

    public static function config(): string
    {
        return __DIR__ . '/../../../sailor.php';
    }
}
