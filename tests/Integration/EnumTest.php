<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Integration;

use Spawnia\Sailor\Enum\MyCustomEnumQuery;
use Spawnia\Sailor\Enum\MyDefaultEnumQuery;
use Spawnia\Sailor\Enum\MyEnumInputQuery;
use Spawnia\Sailor\Enum\Types\CustomEnum;
use Spawnia\Sailor\Enum\Types\DefaultEnum;
use Spawnia\Sailor\Enum\Types\EnumInput;
use Spawnia\Sailor\Tests\TestCase;

class EnumTest extends TestCase
{
    public function testDefault(): void
    {
        $value = DefaultEnum::A;

        MyDefaultEnumQuery::mock()
            ->expects('execute')
            ->once()
            ->with($value)
            ->andReturn(MyDefaultEnumQuery\MyDefaultEnumQueryResult::fromStdClass((object) [
                'data' => (object) [
                    'withDefaultEnum' => $value,
                ],
            ]));

        $result = MyDefaultEnumQuery::execute($value)->errorFree();
        self::assertSame($value, $result->data->withDefaultEnum);
    }

    public function testCustom(): void
    {
        $value = new CustomEnum(CustomEnum::A);

        MyCustomEnumQuery::mock()
            ->expects('execute')
            ->once()
            ->with($value)
            ->andReturn(MyCustomEnumQuery\MyCustomEnumQueryResult::fromStdClass((object) [
                'data' => (object) [
                    'withCustomEnum' => $value->value,
                ],
            ]));

        $result = MyCustomEnumQuery::execute($value)->errorFree();

        $customEnum = $result->data->withCustomEnum;
        self::assertInstanceOf(CustomEnum::class, $customEnum);
        self::assertSame($value->value, $customEnum->value);
    }

    public function testEnumInput(): void
    {
        $custom = new CustomEnum(CustomEnum::B);
        $default = DefaultEnum::B;

        $input = new EnumInput();
        $input->custom = $custom;
        $input->default = $default;

        MyEnumInputQuery::mock()
            ->expects('execute')
            ->once()
            ->with($input)
            ->andReturn(MyEnumInputQuery\MyEnumInputQueryResult::fromStdClass((object) [
                'data' => (object) [
                    'withEnumInput' => $input->toGraphQL($input),
                ],
            ]));

        $result = MyEnumInputQuery::execute($input)->errorFree();
        $enumObject = $result->data->withEnumInput;
        self::assertNotNull($enumObject);

        $customEnum = $enumObject->custom;
        self::assertInstanceOf(CustomEnum::class, $customEnum);
        self::assertSame($input->custom->value, $customEnum->value);

        self::assertSame($input->default, $enumObject->default);
    }
}
