<?php

namespace Spawnia\Sailor\Testing;

use Spawnia\Sailor\Operation;

trait UsesSailorMocks
{
    /**
     * @after
     */
    protected function tearDownSailorMocks(): void
    {
        Operation::clearMocks();
    }
}
