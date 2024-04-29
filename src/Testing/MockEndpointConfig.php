<?php declare(strict_types=1);

namespace Spawnia\Sailor\Testing;

use Spawnia\Sailor\Client;
use Spawnia\Sailor\Codegen\Finder;
use Spawnia\Sailor\EndpointConfig;
use Spawnia\Sailor\Response;

class MockEndpointConfig extends EndpointConfig
{
    public MockClient $client;

    public string $namespace = '';

    public string $targetPath;

    public Finder $finder;

    public string $schemaPath;

    public function namespace(): string
    {
        return $this->namespace;
    }

    public function targetPath(): string
    {
        return $this->targetPath;
    }

    public function schemaPath(): string
    {
        return $this->schemaPath;
    }

    public function finder(): Finder
    {
        return $this->finder;
    }

    public function makeClient(): Client
    {
        return new MockClient(static function (): Response {
            throw new \Exception('No response configured.');
        });
    }
}
