<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit;

use Mockery;
use PHPUnit\Framework\TestCase;
use Spawnia\Sailor\Configuration;
use Spawnia\Sailor\EndpointConfig;
use Spawnia\Sailor\Error\Error;
use Spawnia\Sailor\Error\ResultErrorsException;
use Spawnia\Sailor\Simple\Operations\MyScalarQuery\MyScalarQuery;
use Spawnia\Sailor\Simple\Operations\MyScalarQuery\MyScalarQueryResult;

class ResultTest extends TestCase
{
    /**
     * @dataProvider isClientSafe
     */
    public function testThrowErrors(bool $isClientSafe): void
    {
        $endpoint = Mockery::mock(EndpointConfig::class);
        $endpoint->expects('errorsAreClientSafe')
            ->once()
            ->andReturn($isClientSafe);
        Configuration::setEndpoint(MyScalarQueryResult::endpoint(), $endpoint);

        $result = new MyScalarQueryResult();

        // No errors, so nothing happens
        $result->assertErrorFree();

        $errors = [new Error('foo')];
        $result->errors = $errors;

        $exception = null;
        try {
            $result->assertErrorFree();
        } catch (\Throwable $e) {
            $exception = $e;
        }

        self::assertInstanceOf(ResultErrorsException::class, $exception);
        self::assertSame($isClientSafe, $exception->isClientSafe());
    }

    /**
     * @return iterable<array{bool}>
     */
    public function isClientSafe(): iterable
    {
        yield [true];
        yield [false];
    }

    /**
     * @dataProvider isClientSafe
     */
    public function testErrorFree(bool $isClientSafe): void
    {
        $endpoint = Mockery::mock(EndpointConfig::class);
        $endpoint->expects('errorsAreClientSafe')
            ->once()
            ->andReturn($isClientSafe);
        Configuration::setEndpoint(MyScalarQueryResult::endpoint(), $endpoint);

        $result = new MyScalarQueryResult();
        $result->data = MyScalarQuery::fromStdClass((object) [
            'scalarWithArg' => null,
        ]);

        // No errors
        $result->errorFree();

        $result->errors = [new Error('foo')];

        $exception = null;
        try {
            $result->errorFree();
        } catch (\Throwable $e) {
            $exception = $e;
        }

        self::assertInstanceOf(ResultErrorsException::class, $exception);
        self::assertSame($isClientSafe, $exception->isClientSafe());
    }

    public function testWithErrors(): void
    {
        $endpoint = Mockery::mock(EndpointConfig::class)->makePartial();
        Configuration::setEndpoint(MyScalarQueryResult::endpoint(), $endpoint);

        $message = 'foo';

        $result = MyScalarQueryResult::fromStdClass((object) [
            'errors' => [
                (object) [
                    'message' => $message,
                ],
            ],
        ]);

        self::assertNull($result->data);

        $errors = $result->errors;
        self::assertNotNull($errors);
        self::assertCount(1, $errors);

        $error = $errors[0];
        self::assertSame($message, $error->message);

        self::assertNull($result->extensions);
    }

    /**
     * @dataProvider isClientSafe
     */
    public function testFromErrors(bool $isClientSafe): void
    {
        $endpoint = Mockery::mock(EndpointConfig::class)->makePartial();
        $endpoint->expects('errorsAreClientSafe')
            ->once()
            ->andReturn($isClientSafe);
        Configuration::setEndpoint(MyScalarQueryResult::endpoint(), $endpoint);

        $result = MyScalarQueryResult::fromErrors([
            (object) [
                'message' => 'foo',
            ],
        ]);
        self::assertNull($result->data);

        $errors = $result->errors;
        self::assertNotNull($errors);
        self::assertCount(1, $errors);
        $error = $errors[0];
        self::assertSame($isClientSafe, $error->isClientSafe());

        self::assertNull($result->extensions);
    }

    public function testFromData(): void
    {
        $data = MyScalarQuery::make(
        /* scalarWithArg: */
            'bar'
        );
        $result = MyScalarQueryResult::fromData($data);

        self::assertSame($data, $result->data);
        self::assertNull($result->errors);
        self::assertNull($result->extensions);
    }
}
