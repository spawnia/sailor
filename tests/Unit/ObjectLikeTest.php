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
            '__typename' => 'Query',
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

    public function testExtraneousKey(): void
    {
        $this->expectExceptionObject(new InvalidDataException(
            'simple: Unknown property nonExistent, available properties: __typename, scalarWithArg.'
        ));
        MyScalarQuery::fromStdClass((object) [
            '__typename' => 'Query',
            'scalarWithArg' => 'foo',
            'nonExistent' => 'bar',
        ]);
    }

    public function testMissingField(): void
    {
        $this->expectExceptionObject(new InvalidDataException(
            'simple: Missing field __typename.'
        ));
        MyScalarQuery::fromStdClass((object) []);
    }

    public function testMissingRequiredValue(): void
    {
        $this->expectExceptionObject(new InvalidDataException(
            'simple: Invalid value for field __typename. Expected non-null value, got null'
        ));
        MyScalarQuery::fromStdClass((object) [
            '__typename' => null,
        ]);
    }
}
