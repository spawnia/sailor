<?php declare(strict_types=1);

namespace Spawnia\Sailor\Events;

use stdClass;

/**
 * Fired after calling `execute()` on an `Operation`, before invoking the client.
 */
class StartRequest
{
    public string $document;

    public stdClass $variables;

    public function __construct(string $document, stdClass $variables)
    {
        $this->document = $document;
        $this->variables = $variables;
    }
}
