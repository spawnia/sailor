<?php

declare(strict_types=1);

namespace Spawnia\Sailor\PhpKeywords\Operations\_catch;

class _catchResult extends \Spawnia\Sailor\Result
{
    public ?_catch $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = _catch::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(_catch $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): _catchErrorFreeResult
    {
        return _catchErrorFreeResult::fromResult($this);
    }

    public static function endpoint(): string
    {
        return 'php-keywords';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../sailor.php');
    }
}
