<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Input\Operations;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\Input\Operations\TakeSomeInput\TakeSomeInputResult>
 */
class TakeSomeInput extends \Spawnia\Sailor\Operation
{
    public static function execute(?\Spawnia\Sailor\Input\Types\SomeInput $input = null): TakeSomeInput\TakeSomeInputResult
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
        return 'input';
    }
}
