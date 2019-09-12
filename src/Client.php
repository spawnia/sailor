<?php

declare(strict_types=1);

namespace Spawnia\Sailor;

interface Client
{
    public function request(string $query, \stdClass $variables = null): Response;
}
