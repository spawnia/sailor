<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Spawnia\Sailor\Foo\Foo\FooResult;
use Spawnia\Sailor\ResultErrorsException;

class ResultTest extends TestCase
{
    public function testThrowErrors(): void
    {
        $result = new FooResult();

        // No errors, so nothing happens
        $result->throwErrors();

        $result->errors = [new \stdClass()];

        $this->expectException(ResultErrorsException::class);
        $result->throwErrors();
    }
}
