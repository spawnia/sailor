<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit\Testing;

use Spawnia\Sailor\Simple\Operations\MyScalarQuery;
use Spawnia\Sailor\Testing\RequiresSailorMocks;
use Spawnia\Sailor\Tests\TestCase;

final class RequiresSailorMocksTest extends TestCase
{
    use RequiresSailorMocks;

    public function testThrowsOnMissingCalls(): void
    {
        $this->expectExceptionObject(new \Exception('Tried to execute a Sailor operation on endpoint simple, but no mock for was registered for Spawnia\\Sailor\\Simple\\Operations\\MyScalarQuery.'));
        MyScalarQuery::execute();
    }
}
