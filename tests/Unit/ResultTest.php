<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Spawnia\Sailor\ResultErrorsException;
use Spawnia\Sailor\Simple\MyScalarQuery\MyScalarQuery;
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

    public function testErrorFree(): void
    {
        $result = new MyScalarQueryResult();
        $result->data = MyScalarQuery::fromStdClass((object) [
            'scalarWithArg' => null,
        ]);

        // No errors
        $result->errorFree();

        $result->errors = [new \stdClass()];

        $this->expectException(ResultErrorsException::class);
        $result->errorFree();
    }

    public function testWithErrors(): void
    {
        $result = MyScalarQueryResult::fromStdClass((object) [
            'errors' => [
                (object) [
                    'message' => 'foo',
                ],
            ],
        ]);
        self::assertNull($result->data);
        self::assertNotNull($result->errors);
        self::assertCount(1, $result->errors);
        self::assertNull($result->extensions);
    }
}
