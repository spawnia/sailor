<?php declare(strict_types=1);

namespace Spawnia\Sailor\InlineFragments\Operations\SearchQuery;

class SearchQueryResult extends \Spawnia\Sailor\Result
{
    public ?SearchQuery $data = null;

    protected function setData(\stdClass $data): void
    {
        $this->data = SearchQuery::fromStdClass($data);
    }

    /**
     * Useful for instantiation of successful mocked results.
     *
     * @return static
     */
    public static function fromData(SearchQuery $data): self
    {
        $instance = new static;
        $instance->data = $data;

        return $instance;
    }

    public function errorFree(): SearchQueryErrorFreeResult
    {
        return SearchQueryErrorFreeResult::fromResult($this);
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
