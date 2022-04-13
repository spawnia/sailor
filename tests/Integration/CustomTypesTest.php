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

final class CustomTypesTest extends TestCase
{
    public function testDefaultEnum(): void
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

    public function testCustomEnum(): void
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

    public function testConvertsCustomEnumArgumentBeforeSendingIt(): void
    {
        $value = CustomEnum::A;

        $client = Mockery::mock(Client::class);
        $client->expects('request')
            ->once()
            ->withArgs(function (string $query, \stdClass $variables) use ($value): bool {
                return $query === MyCustomEnumQuery::document()
                    && $variables->value === $value;
            })
            ->andReturn(Response::fromStdClass((object) [
                'data' => null,
            ]));

        $endpoint = Mockery::mock(EndpointConfig::class);
        $endpoint->expects('makeClient')
            ->once()
            ->withNoArgs()
            ->andReturn($client);

        Configuration::setEndpoint(MyCustomEnumQuery::endpoint(), $endpoint);

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
            ->andReturn(MyBenSampoEnumQuery\MyBenSampoEnumQueryResult::fromStdClass((object) [
                'data' => (object) [
                    'withBenSampoEnum' => $value->value,
                ],
            ]));

        $result = MyBenSampoEnumQuery::execute($value)->errorFree();

        $benSampoEnum = $result->data->withBenSampoEnum;
        self::assertInstanceOf(BenSampoEnum::class, $benSampoEnum);
        self::assertSame($value->value, $benSampoEnum->value);
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
                    'withEnumInput' => $input->toStdClass(),
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
