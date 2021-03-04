<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Spawnia\Sailor\Operation;

class TestCase extends PHPUnitTestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        Operation::clearMocks();
    }
}
