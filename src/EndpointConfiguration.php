<?php

declare(strict_types=1);

namespace Spawnia\Sailor;

interface EndpointConfiguration
{
    public function client(): Client;
}
