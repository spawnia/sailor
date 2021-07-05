<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit\Client;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Spawnia\Sailor\Client\Guzzle;
use Spawnia\Sailor\Client\Log;

class LogTest extends TestCase
{
    const FILENAME = __DIR__ . '/LogTest.log';
    const EXPECTED_JSON = /** @lang JSON */ '{"query":"{ foo }","variables":{"bar":42}}' . "\n";

    protected function tearDown(): void
    {
        if (file_exists(self::FILENAME)) {
            unlink(self::FILENAME);
        }

        parent::tearDown();
    }

    public function testRequest(): void
    {
        self::assertFileDoesNotExist(self::FILENAME);

        $log = new Log(self::FILENAME);
        $log->request(/** @lang GraphQL */ '{ foo }', (object) ['bar' => 42]);

        $contents = file_get_contents(self::FILENAME);
        $this->assertSame(self::EXPECTED_JSON, $contents);

        $log->request(/** @lang GraphQL */ '{ foo }', (object) ['bar' => 42]);

        $contents = file_get_contents(self::FILENAME);
        $this->assertSame(self::EXPECTED_JSON . self::EXPECTED_JSON, $contents);
    }
}
