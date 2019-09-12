<?php

declare(strict_types=1);

namespace Spawnia\Sailor;

interface EndpointConfig
{
    public function client(): Client;
}
