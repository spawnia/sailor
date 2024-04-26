<?php declare(strict_types=1);

namespace Spawnia\Sailor\Testing;

use PHPUnit\Framework\Attributes\After;
use Spawnia\Sailor\Operation;

trait ClearsSailorClients
{
    /** @after */
    #[After]
    protected function tearDownSailorClients(): void
    {
        Operation::clearClients();
    }
}
