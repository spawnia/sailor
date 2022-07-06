<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations\ReservedKeywords;

class ReservedKeywordsResult extends \Spawnia\Sailor\Result
{
    public ?ReservedKeywords $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = ReservedKeywords::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(ReservedKeywords $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): ReservedKeywordsErrorFreeResult
    {
        return ReservedKeywordsErrorFreeResult::fromResult($this);
    }

    public static function endpoint(): string
    {
        return 'simple';
    }

    public static function config(): string
    {
        return __DIR__ . '/../../../sailor.php';
    }
}
