<?php declare(strict_types=1);

namespace Spawnia\Sailor\Testing;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Before;
use Spawnia\Sailor\Operation;

trait RequiresSailorMocks
{
    /** @before */
    #[Before]
    protected function setUpSailorMocks(): void
    {
        Operation::requireMocks(true);
    }

    /** @after */
    #[After]
    protected function tearDownSailorMocks(): void
    {
        Operation::requireMocks(false);
    }
}
