<?php declare(strict_types=1);

namespace Spawnia\Sailor\Input\Operations;

/**
 * @extends \Spawnia\Sailor\Operation<\Spawnia\Sailor\Input\Operations\TakeSomeInput\TakeSomeInputResult>
 */
class TakeSomeInput extends \Spawnia\Sailor\Operation
{
    /**
     * @param \Spawnia\Sailor\Input\Types\SomeInput|null $input
     */
    public static function execute(
        $input = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): TakeSomeInput\TakeSomeInputResult {
        return self::executeOperation(
            $input,
        );
    }

    protected static function converters(): array
    {
        static $converters;

        return $converters ??= [
            ['input', new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Input\Types\SomeInput)],
        ];
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

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../sailor.php');
    }
}
