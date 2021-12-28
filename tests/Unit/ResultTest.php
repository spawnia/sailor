<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit;

use Mockery;
use PHPUnit\Framework\TestCase;
use Spawnia\Sailor\Configuration;
use Spawnia\Sailor\EndpointConfig;
use Spawnia\Sailor\Error\Error;
use Spawnia\Sailor\ResultErrorsException;
use Spawnia\Sailor\Simple\Operations\MyScalarQuery\MyScalarQuery;
use Spawnia\Sailor\Simple\Operations\MyScalarQuery\MyScalarQueryResult;

class ResultTest extends TestCase
{
    public function testThrowErrors(): void
    {
        $result = new MyScalarQueryResult();

        // No errors, so nothing happens
        $result->assertErrorFree();

        $result->errors = [new Error('foo')];

        $this->expectException(ResultErrorsException::class);
        $result->assertErrorFree();
    }

    public function testErrorFree(): void
    {
        $result = new MyScalarQueryResult();
        $result->data = MyScalarQuery::fromStdClass((object) [
            'scalarWithArg' => null,
        ]);

        // No errors
        $result->errorFree();

        $result->errors = [new Error('foo')];

        $this->expectException(ResultErrorsException::class);
        $result->errorFree();
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

    public function testFromErrors(): void
    {
        $result = MyScalarQueryResult::fromErrors([
            (object) [
                'message' => 'foo',
            ],
        ]);
        self::assertNull($result->data);
        self::assertNotNull($result->errors);
        self::assertCount(1, $result->errors);
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
