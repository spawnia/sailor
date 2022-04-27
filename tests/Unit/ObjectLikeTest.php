<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit;

use Spawnia\Sailor\Error\InvalidDataException;
use Spawnia\Sailor\Simple\Operations\MyScalarQuery\MyScalarQuery;
use Spawnia\Sailor\Tests\TestCase;

final class ObjectLikeTest extends TestCase
{
    public function testFromStdClass(): void
    {
        $bar = 'bar';
        $foo = MyScalarQuery::fromStdClass((object) [
            'scalarWithArg' => $bar,
        ]);

        self::assertSame($bar, $foo->scalarWithArg);
    }

    public function testMake(): void
    {
        $bar = 'bar';
        $foo = MyScalarQuery::make(
            /* scalarWithArg: */
            $bar
        );

        self::assertSame($bar, $foo->scalarWithArg);
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
