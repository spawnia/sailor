<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Spawnia\Sailor\InvalidDataException;
use Spawnia\Sailor\Simple\Operations\MyScalarQuery\MyScalarQuery;

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
        $this->expectExceptionObject(new InvalidDataException(
            'Unknown property nonExistent, available properties: __typename, scalarWithArg.'
        ));
        MyScalarQuery::fromStdClass((object) [
            'nonExistent' => 'foo',
        ]);
    }
}
