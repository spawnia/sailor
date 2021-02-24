<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Testing;

use Spawnia\Sailor\Client;
use Spawnia\Sailor\EndpointConfig;

class MockEndpointConfig extends EndpointConfig
{
    public MockClient $client;

    public string $namespace = '';

    public string $targetPath;

    public string $searchPath;

    public string $schemaPath;

    public function namespace(): string
    {
        return $this->namespace;
    }

    public function targetPath(): string
    {
        return $this->targetPath;
    }

    public function searchPath(): string
    {
        return $this->searchPath;
    }

    public function schemaPath(): string
    {
        return $this->schemaPath;
    }

    public function makeClient(): Client
    {
        return new MockClient();
    }
}
