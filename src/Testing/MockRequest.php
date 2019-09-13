<?php

namespace Spawnia\Sailor\Testing;

class MockRequest
{
    /**
     * @var string
     */
    public $query;

    /**
     * @var \stdClass
     */
    public $variables;

    public function __construct(string $query, \stdClass $variables = null)
    {
        $this->query = $query;
        $this->variables = $variables;
    }
}
