<?php

declare(strict_types=1);

namespace Spawnia\Sailor;

interface EndpointConfig
{
    public function client(): Client;

    public function namespace(): string;

    public function targetPath(): string;

    public function searchPath(): string;

    public function schemaPath(): string;
}
