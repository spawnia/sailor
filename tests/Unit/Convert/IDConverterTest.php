<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit\Convert;

use Spawnia\Sailor\Convert\IDConverter;
use Spawnia\Sailor\Convert\TypeConverter;

final class IDConverterTest extends TypeConverterTest
{
    public function testFromInt(): void
    {
        self::assertSame('1', $this->typeConverter()->fromGraphQL(1));
        self::assertSame('1', $this->typeConverter()->toGraphQL(1));
    }

    protected function typeConverter(): TypeConverter
    {
        return new IDConverter();
    }

    public static function internalExternal(): iterable
    {
        yield ['1', '1'];
        yield ['abc', 'abc'];
    }
}
