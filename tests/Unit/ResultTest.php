<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Spawnia\Sailor\ResultErrorsException;
use Spawnia\Sailor\Simple\MyScalarQuery\MyScalarQueryResult;

class ResultTest extends TestCase
{
    public function testThrowErrors(): void
    {
        $result = new MyScalarQueryResult();

        // No errors, so nothing happens
        $result->assertErrorFree();

        $result->errors = [new \stdClass()];

        $this->expectException(ResultErrorsException::class);
        $result->assertErrorFree();
    }
}
