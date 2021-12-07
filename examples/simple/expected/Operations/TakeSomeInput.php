<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Simple\Operations;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\Simple\Operations\TakeSomeInput\TakeSomeInputResult>
 */
class TakeSomeInput extends \Spawnia\Sailor\Operation
{
    public static function execute(?\Spawnia\Sailor\Simple\Types\SomeInput $input = null): TakeSomeInput\TakeSomeInputResult
    {
        return self::executeOperation(...func_get_args());
    }

    public static function document(): string
    {
        return /* @lang GraphQL */ 'mutation TakeSomeInput($input: SomeInput) {
          __typename
          takeSomeInput(input: $input)
        }';
    }

    public static function endpoint(): string
    {
        return 'simple';
    }
}
