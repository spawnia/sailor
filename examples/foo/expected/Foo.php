<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Foo;

class Foo extends \Spawnia\Sailor\Operation
{
    public static function execute(): Foo\FooResult
    {
        $response = self::fetchResponse();

        return \Spawnia\Sailor\Foo\Foo\FooResult::fromResponse($response);
    }

    public static function document(): string
    {
        return /* @lang GraphQL */ 'query Foo {
            foo
        }
        ';
    }

    public static function endpoint(): string
    {
        return 'foo';
    }
}
