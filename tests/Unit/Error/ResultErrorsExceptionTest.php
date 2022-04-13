<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit\Error;

use Spawnia\Sailor\Error\Error;
use Spawnia\Sailor\Error\ResultErrorsException;
use Spawnia\Sailor\Tests\TestCase;

final class ResultErrorsExceptionTest extends TestCase
{
    public function testConstructor(): void
    {
        $errors = [new Error('bar'), new Error('baz')];
        $exception = new ResultErrorsException($errors, 'foo');

        self::assertSame($errors, $exception->errors);
        self::assertSame('foo: bar | baz', $exception->getMessage());
    }
}
