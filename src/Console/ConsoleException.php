<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Console;

class ConsoleException extends \Exception
{
    public static function missingEndpoint(string $endpoint): self
    {
        return new self(
            "The given endpoint $endpoint does not exist in the configuration. You may want to add it to your \"sailor.php\"."
        );
    }
}
