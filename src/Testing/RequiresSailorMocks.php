<?php declare(strict_types=1);

namespace Spawnia\Sailor\Testing;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Before;
use Spawnia\Sailor\Operation;

trait RequiresSailorMocks
{
    /** @before */
    #[Before]
    protected function setUpRequiresSailorMocks(): void
    {
        Operation::requireMocks(true);
    }

    /** @after */
    #[After]
    protected function tearDownRequiresSailorMocks(): void
    {
        Operation::requireMocks(false);
        Operation::clearMocks();
    }
}
