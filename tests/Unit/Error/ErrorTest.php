<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit\Error;

use PHPUnit\Framework\TestCase;
use Spawnia\Sailor\Error\Error;

class ErrorTest extends TestCase
{
    public function testOnlyMessage(): void
    {
        $message = 'some message';

        $error = Error::fromStdClass((object) [
            'message' => $message,
        ]);

        self::assertSame($message, $error->message);
        self::assertNull($error->locations);
        self::assertNull($error->path);
        self::assertNull($error->extensions);
    }

    public function testFull(): void
    {
        $message = 'some message';

        $path = [
            'foo',
            1,
            'bar',
        ];

        $extensions = (object) [
            'foo' => 123,
        ];

        $error = Error::fromStdClass((object) [
            'message' => $message,
            'locations' => [
                (object) [
                    'line' => 1,
                    'column' => 2,
                ],
                (object) [
                    'line' => 3,
                    'column' => 4,
                ],
            ],
            'path' => $path,
            'extensions' => $extensions,
        ]);

        self::assertSame($message, $error->message);

        $locations = $error->locations;
        self::assertIsArray($locations);
        self::assertCount(2, $locations);
        [$location1, $location2] = $locations;
        self::assertSame(1, $location1->line);
        self::assertSame(2, $location1->column);
        self::assertSame(3, $location2->line);
        self::assertSame(4, $location2->column);

        self::assertSame($path, $error->path);

        self::assertEquals($extensions, $error->extensions);

        self::assertJsonStringEqualsJsonString(/** @lang JSON */ <<<'JSON'
{
    "message": "some message",
    "locations": [
        {
            "line": 1,
            "column": 2
        },
        {
            "line": 3,
            "column": 4
        }
    ],
    "path": [
        "foo",
        1,
        "bar"
    ],
    "extensions": {
        "foo": 123
    },
    "isClientSafe": false
}
JSON
, \Safe\json_encode($error));
    }

    public function testClientAware(): void
    {
        $error = Error::fromStdClass((object) [
            'message' => 'irrelevant',
        ]);
        self::assertFalse($error->isClientSafe());

        $error->isClientSafe = true;
        self::assertTrue($error->isClientSafe());
    }
}
