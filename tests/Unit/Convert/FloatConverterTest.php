<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit\Convert;

use Spawnia\Sailor\Convert\FloatConverter;
use Spawnia\Sailor\Convert\TypeConverter;

final class FloatConverterTest extends TypeConverterTest
{
    public function testAcceptsInt(): void
    {
        $float = 0.0;
        $value = \Safe\json_decode(\Safe\json_encode($float));
        self::assertSame(0, $value);

        self::assertSame($float, $this->typeConverter()->fromGraphQL($value));
    }

    protected function typeConverter(): TypeConverter
    {
        return new FloatConverter();
    }

    public static function internalExternal(): iterable
    {
        yield [0.0, 0.0];
        yield [0.1, 0.1];
        yield [-0.1, -0.1];
    }
}
