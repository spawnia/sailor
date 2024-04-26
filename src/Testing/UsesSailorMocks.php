<?php declare(strict_types=1);

namespace Spawnia\Sailor\Testing;

use PHPUnit\Framework\Attributes\After;
use Spawnia\Sailor\Operation;

trait UsesSailorMocks
{
    /** @after */
    #[After]
    protected function tearDownSailorMocks(): void
    {
        Operation::clearMocks();
    }
}
