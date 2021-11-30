<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit\Client;

use PHPUnit\Framework\TestCase;
use Spawnia\Sailor\Client\Log;

class LogTest extends TestCase
{
    public const FILENAME = __DIR__ . '/LogTest.log';

    public const QUERY = /** @lang GraphQL */ '{ foo }';
    public const EXPECTED_JSON = /** @lang JSON */ '{"query":"{ foo }","variables":{"bar":42}}' . "\n";
    public const VARIABLES = ['bar' => 42];

    protected function tearDown(): void
    {
        if (file_exists(self::FILENAME)) {
            \Safe\unlink(self::FILENAME);
        }

        parent::tearDown();
    }

    public function testRequest(): void
    {
        self::assertFileDoesNotExist(self::FILENAME);

        $log = new Log(self::FILENAME);
        $log->request(/** @lang GraphQL */ self::QUERY, (object) self::VARIABLES);

        self::assertFileExists(self::FILENAME);
        $contents = \Safe\file_get_contents(self::FILENAME);
        self::assertSame(self::EXPECTED_JSON, $contents);

        $log->request(/** @lang GraphQL */ self::QUERY, (object) self::VARIABLES);

        $contents = \Safe\file_get_contents(self::FILENAME);
        self::assertSame(self::EXPECTED_JSON . self::EXPECTED_JSON, $contents);
    }

    public function testRequests(): void
    {
        $log = new Log(self::FILENAME);
        $log->request(/** @lang GraphQL */ self::QUERY, (object) self::VARIABLES);
        $log->request(/** @lang GraphQL */ self::QUERY, null);

        $decoded = iterator_to_array($log->requests());
        self::assertSame([
            [
                'query' => self::QUERY,
                'variables' => self::VARIABLES,
            ],
            [
                'query' => self::QUERY,
                'variables' => null,
            ],
        ], $decoded);
    }

    public function testClear(): void
    {
        \Safe\file_put_contents(self::FILENAME, 'foo');

        $log = new Log(self::FILENAME);
        $log->clear();

        self::assertFileDoesNotExist(self::FILENAME);
    }
}
