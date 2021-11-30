<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Spawnia\Sailor\InvalidResponseException;
use Spawnia\Sailor\Simple\MyScalarQuery\MyScalarQuery;

class TypedObjectTest extends TestCase
{
    public function testDecode(): void
    {
        $foo = MyScalarQuery::fromStdClass((object) [
            'scalarWithArg' => 'bar',
        ]);

        self::assertSame('bar', $foo->scalarWithArg);
    }

    public function testWrongKey(): void
    {
        $this->expectException(InvalidResponseException::class);
        $this->expectExceptionMessage('Unknown field nonExistent, available fields: __typename, scalarWithArg.');
        MyScalarQuery::fromStdClass((object) [
            'nonExistent' => 'foo',
        ]);
    }
}
