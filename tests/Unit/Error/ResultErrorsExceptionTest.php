<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit\Error;

use PHPUnit\Framework\TestCase;
use Spawnia\Sailor\Error\Error;
use Spawnia\Sailor\Error\ResultErrorsException;

class ResultErrorsExceptionTest extends TestCase
{
    public function testConstructor(): void
    {
        $errors = [new Error('test message')];
        $exception = new ResultErrorsException($errors, 'test');

        self::assertSame($errors, $exception->errors);
        self::assertSame('test: test message', $exception->getMessage());
    }
}
