<?php declare(strict_types=1);

namespace Spawnia\Sailor\InlineFragments\Operations\SkipArticleContentSelectVideoContent;

class SkipArticleContentSelectVideoContentResult extends \Spawnia\Sailor\Result
{
    public ?SkipArticleContentSelectVideoContent $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = SkipArticleContentSelectVideoContent::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(SkipArticleContentSelectVideoContent $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): SkipArticleContentSelectVideoContentErrorFreeResult
    {
        return SkipArticleContentSelectVideoContentErrorFreeResult::fromResult($this);
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
