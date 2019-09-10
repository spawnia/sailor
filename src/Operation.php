<?php

declare(strict_types=1);

namespace Spawnia\Sailor;

/**
 * Subclasses of this class are automatically generated.
 *
 * They must implement the following abstract function:
 * public abstract function run(mixed[] ...$args): mixed
 */
abstract class Operation
{
    protected function runInternal(string $document)
    {
        // Run the actual operation against the server
    }
}
