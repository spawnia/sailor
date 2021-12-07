<?php declare(strict_types=1);

namespace Spawnia\Sailor\Simple;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\Simple\TakeSomeInput\TakeSomeInputResult>
 */
class TakeSomeInput extends \Spawnia\Sailor\Operation
{
    public static function execute(?Inputs\SomeInput $input = null): TakeSomeInput\TakeSomeInputResult
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
