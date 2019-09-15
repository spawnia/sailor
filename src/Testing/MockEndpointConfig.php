<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Testing;

use Spawnia\Sailor\Client;
use Spawnia\Sailor\EndpointConfig;

class MockEndpointConfig implements EndpointConfig
{
    /** @var MockClient */
    public $mockClient;

    /** @var string */
    public $namespace = '';

    /** @var string */
    public $targetPath;

    /** @var string */
    public $searchPath;

    /** @var string */
    public $schemaPath;

    public function __construct()
    {
        $this->mockClient = new MockClient();
    }

    public function client(): Client
    {
        return $this->mockClient;
    }

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
}
