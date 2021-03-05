<?php

declare(strict_types=1);

namespace Spawnia\Sailor;

use Spawnia\Sailor\Console\ConsoleException;

class ConfigurationException extends \Exception
{
    public static function wrongReturnType(string $type): self
    {
        return new self("Expected `sailor.php` to return an array, got: {$type}.");
    }

    public static function missingEndpoint(string $endpoint): ConsoleException
    {
        return new ConsoleException(
            "The given endpoint {$endpoint} does not exist in the configuration. You may want to add it to your `sailor.php`."
        );
    }
}
