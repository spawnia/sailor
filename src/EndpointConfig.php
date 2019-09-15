<?php

declare(strict_types=1);

namespace Spawnia\Sailor;

use Spawnia\Sailor\Testing\MockClient;

abstract class EndpointConfig
{
    /** @var MockClient */
    public $mockClient;

    public abstract function makeClient(): Client;

    public function client(): Client
    {
        if (isset($this->mockClient)) {
            return $this->mockClient;
        }

        return $this->makeClient();
    }

    public abstract function namespace(): string;

    public abstract function targetPath(): string;

    public abstract function searchPath(): string;

    public abstract function schemaPath(): string;
}
