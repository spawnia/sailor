<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple;

class TwoArgs extends \Spawnia\Sailor\Operation
{
    public static function execute(?string $first = null, ?int $second = null): TwoArgs\TwoArgsResult
    {
        return self::executeOperation(...func_get_args());
    }

    public static function document(): string
    {
        return /* @lang GraphQL */ 'query TwoArgs($first: String, $second: Int) {
          twoArgs(first: $first, second: $second)
        }';
    }

    public static function endpoint(): string
    {
        return 'simple';
    }
}
