<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Foo;

class Bar extends \Spawnia\Sailor\Operation
{
    public static function execute(): Bar\BarResult
    {
        $response = self::fetchResponse(...func_get_args());

        return \Spawnia\Sailor\Foo\Bar\BarResult::fromResponse($response);
    }

    public static function document(): string
    {
        return /* @lang GraphQL */ 'query Bar {
          bar {
            baz
          }
        }';
    }

    public static function endpoint(): string
    {
        return 'foo';
    }
}
