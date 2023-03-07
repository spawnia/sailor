<?php declare(strict_types=1);

namespace Spawnia\Sailor\Testing;

use Spawnia\Sailor\Operation;

trait ClearsSailorClients
{
    /**
     * @after
     */
    protected function tearDownSailorClients(): void
    {
        Operation::clearClients();
    }
}
