<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Tests;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Spawnia\Sailor\Testing\UsesSailorMocks;

abstract class TestCase extends PHPUnitTestCase
{
    use MockeryPHPUnitIntegration;
    use UsesSailorMocks;
}
