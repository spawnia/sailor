<?php declare(strict_types=1);

namespace Spawnia\Sailor;

final class ConfigurationException extends \Exception
{
    public static function wrongReturnType(string $type): self
    {
        return new self("Expected `sailor.php` to return an array, got: {$type}.");
    }

    public static function missingEndpoint(string $endpoint): self
    {
        return new self("The given endpoint {$endpoint} does not exist in the configuration. You may want to add it to your `sailor.php`.");
    }

    public static function missingFile(string $file): self
    {
        return new self("The requested configuration file {$file} does not exist. An example configuration has been created, modify it to your needs and try again.");
    }
}
