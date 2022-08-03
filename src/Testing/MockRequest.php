<?php declare(strict_types=1);

namespace Spawnia\Sailor\Testing;

use stdClass;

class MockRequest
{
    public string $query;

    public ?stdClass $variables;

    public function __construct(string $query, ?stdClass $variables = null)
    {
        $this->query = $query;
        $this->variables = $variables;
    }
}
