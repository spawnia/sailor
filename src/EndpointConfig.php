<?php

declare(strict_types=1);

namespace Spawnia\Sailor;

use Spawnia\Sailor\Testing\MockClient;

abstract class EndpointConfig
{
    /** @var MockClient */
    public $mockClient;

    abstract public function makeClient(): Client;

    public function client(): Client
    {
        if (isset($this->mockClient)) {
            return $this->mockClient;
        }

        return $this->makeClient();
    }

    abstract public function namespace(): string;

    abstract public function targetPath(): string;

    abstract public function searchPath(): string;

    abstract public function schemaPath(): string;
}
