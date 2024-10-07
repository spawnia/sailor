<?php declare(strict_types=1);

namespace Spawnia\Sailor\PhpKeywords\Operations\AllCases;

class AllCasesResult extends \Spawnia\Sailor\Result
{
    public ?AllCases $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = AllCases::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(AllCases $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): AllCasesErrorFreeResult
    {
        return AllCasesErrorFreeResult::fromResult($this);
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
