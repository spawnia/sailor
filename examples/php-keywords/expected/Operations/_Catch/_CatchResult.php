<?php declare(strict_types=1);

namespace Spawnia\Sailor\PhpKeywords\Operations\_Catch;

class _CatchResult extends \Spawnia\Sailor\Result
{
    public ?_Catch $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = _Catch::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(_Catch $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): _CatchErrorFreeResult
    {
        return _CatchErrorFreeResult::fromResult($this);
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
