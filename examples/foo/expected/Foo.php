<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Foo;

class Foo extends \Spawnia\Sailor\Operation
{
    public static function execute(?string $bar = null): Foo\FooResult
    {
        $response = self::fetchResponse(...func_get_args());

        return \Spawnia\Sailor\Foo\Foo\FooResult::fromResponse($response);
    }

    public static function document(): string
    {
        return /* @lang GraphQL */ 'query Foo($bar: String) {
          foo(bar: $bar)
        }';
    }

    public static function endpoint(): string
    {
        return 'foo';
    }
}
