<?php

declare(strict_types=1);

namespace Spawnia\Sailor;

abstract class EndpointConfig
{
    /** @var Client */
    public $client;

    public abstract function makeClient(): Client;

    public function client(): Client
    {
        if (!$this->client) {
            $this->client = $this->makeClient();
        }

        return $this->client;
    }

    public function mockClient(Client $client): void
    {
        $this->client = $client;
    }

    public abstract function namespace(): string;

    public abstract function targetPath(): string;

    public abstract function searchPath(): string;

    public abstract function schemaPath(): string;
}
