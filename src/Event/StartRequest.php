<?php declare(strict_types=1);

namespace Spawnia\Sailor\Event;

class StartRequest
{
    public string $document;

    public \stdClass $variables;

    public function __construct(string $document, \stdClass $variables)
    {
        $this->document = $document;
        $this->variables = $variables;
    }
}
