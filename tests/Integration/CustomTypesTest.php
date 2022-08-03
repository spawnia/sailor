<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Integration;

use Mockery;
use Spawnia\Sailor\Client;
use Spawnia\Sailor\Configuration;
use Spawnia\Sailor\CustomTypes\Operations\MyBenSampoEnumQuery;
use Spawnia\Sailor\CustomTypes\Operations\MyCustomEnumQuery;
use Spawnia\Sailor\CustomTypes\Operations\MyDefaultEnumQuery;
use Spawnia\Sailor\CustomTypes\Operations\MyEnumInputQuery;
use Spawnia\Sailor\CustomTypes\Types\BenSampoEnum;
use Spawnia\Sailor\CustomTypes\Types\CustomEnum;
use Spawnia\Sailor\CustomTypes\Types\DefaultEnum;
use Spawnia\Sailor\CustomTypes\Types\EnumInput;
use Spawnia\Sailor\EndpointConfig;
use Spawnia\Sailor\Response;
use Spawnia\Sailor\Tests\TestCase;
use stdClass;

final class CustomTypesTest extends TestCase
{
    public function testDefaultEnum(): void
    {
        $value = DefaultEnum::A;

        MyDefaultEnumQuery::mock()
            ->expects('execute')
            ->once()
            ->with($value)
            ->andReturn(MyDefaultEnumQuery\MyDefaultEnumQueryResult::fromData(
                MyDefaultEnumQuery\MyDefaultEnumQuery::make(
                    /* withDefaultEnum: */
                    $value,
                )
            ));

        $result = MyDefaultEnumQuery::execute($value)->errorFree();
        self::assertSame($value, $result->data->withDefaultEnum);
    }

    public function testCustomEnum(): void
    {
        $value = new CustomEnum(CustomEnum::A);

        MyCustomEnumQuery::mock()
            ->expects('execute')
            ->once()
            ->with($value)
            ->andReturn(MyCustomEnumQuery\MyCustomEnumQueryResult::fromData(
                MyCustomEnumQuery\MyCustomEnumQuery::make(
                    /* withCustomEnum: */
                    $value,
                )
            ));

        $result = MyCustomEnumQuery::execute($value)->errorFree();

        $customEnum = $result->data->withCustomEnum;
        self::assertInstanceOf(CustomEnum::class, $customEnum);
        self::assertSame($value->value, $customEnum->value);
    }

    public function testConvertsCustomEnumArgumentBeforeSendingIt(): void
    {
        $value = CustomEnum::A;

        $client = Mockery::mock(Client::class);
        $client->expects('request')
            ->once()
            ->withArgs(fn (string $query, stdClass $variables): bool => $query === MyCustomEnumQuery::document()
                && $variables->value === $value)
            ->andReturn(Response::fromStdClass((object) [
                'data' => null,
            ]));

        $endpoint = Mockery::mock(EndpointConfig::class);
        $endpoint->expects('makeClient')
            ->once()
            ->withNoArgs()
            ->andReturn($client);

        $endpoint->expects('fireEvent')
            ->twice();

        Configuration::setEndpointFor(MyCustomEnumQuery::class, $endpoint);

        $result = MyCustomEnumQuery::execute(new CustomEnum($value));
        self::assertNull($result->data);
    }

    public function testBenSampoEnum(): void
    {
        $value = BenSampoEnum::A();

        MyBenSampoEnumQuery::mock()
            ->expects('execute')
            ->once()
            ->with($value)
            ->andReturn(MyBenSampoEnumQuery\MyBenSampoEnumQueryResult::fromData(
                MyBenSampoEnumQuery\MyBenSampoEnumQuery::make(
                    /* withBenSampoEnum: */
                    $value,
                ),
            ));

        $result = MyBenSampoEnumQuery::execute($value)->errorFree();

        $benSampoEnum = $result->data->withBenSampoEnum;
        self::assertInstanceOf(BenSampoEnum::class, $benSampoEnum);
        self::assertSame($value->value, $benSampoEnum->value);
    }

    public function testEnumInput(): void
    {
        $custom = new CustomEnum(CustomEnum::B);
        $default = DefaultEnum::B;

        $input = EnumInput::make(
            /* default: */
            $default,
            /* custom: */
            $custom,
        );

        MyEnumInputQuery::mock()
            ->expects('execute')
            ->once()
            ->with($input)
            ->andReturn(MyEnumInputQuery\MyEnumInputQueryResult::fromData(
                MyEnumInputQuery\MyEnumInputQuery::make(
                    /* withEnumInput: */
                    MyEnumInputQuery\WithEnumInput\EnumObject::make(
                        /* custom: */
                        $custom,
                        /* default: */
                        $default,
                    )
                ),
            ));

        $result = MyEnumInputQuery::execute($input)
            ->errorFree();
        $enumObject = $result->data->withEnumInput;
        self::assertNotNull($enumObject);

        $customEnum = $enumObject->custom;
        self::assertInstanceOf(CustomEnum::class, $customEnum);
        self::assertSame($custom->value, $customEnum->value);
        self::assertSame($default, $enumObject->default);
    }
}
