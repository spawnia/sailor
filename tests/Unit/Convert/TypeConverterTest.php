<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit\Convert;

use Spawnia\Sailor\Convert\TypeConverter;
use Spawnia\Sailor\Json;
use Spawnia\Sailor\Tests\TestCase;

/**
 * @phpstan-import-type StdClassJsonValue from Json
 */
abstract class TypeConverterTest extends TestCase
{
    /**
     * @dataProvider internalExternal
     *
     * @param mixed $internal any value
     * @param StdClassJsonValue $external
     */
    public function testFromGraphQL($internal, $external): void
    {
        self::assertSame($internal, $this->typeConverter()->fromGraphQL($external));
    }

    /**
     * @dataProvider internalExternal
     *
     * @param mixed $internal any value
     * @param StdClassJsonValue $external
     */
    public function testToGraphQL($internal, $external): void
    {
        self::assertSame($external, $this->typeConverter()->toGraphQL($internal));
    }

    abstract protected function typeConverter(): TypeConverter;

    /**
     * @return iterable<array{mixed, StdClassJsonValue}>
     */
    abstract public static function internalExternal(): iterable;
}
