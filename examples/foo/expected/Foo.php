<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Foo;

class Foo extends \Spawnia\Sailor\Operation
{
    const DOCUMENT = "query Foo {\n    foo\n}\n";

    public static function run(): Foo\FooResult
    {
        $instance = new self;

        return $instance->runInternal(self::DOCUMENT);
    }
}
