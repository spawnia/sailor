<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Integration;

use Spawnia\Sailor\Input\Operations\TakeSomeInput;
use Spawnia\Sailor\Input\Types\SomeInput;
use Spawnia\Sailor\Tests\TestCase;

class InputTest extends TestCase
{
    public function testSomeInput(): void
    {
        $nestedInput = new SomeInput();
        $nestedInput->required = 'bar';

        $someInput = new SomeInput();
        $someInput->required = 'foo';
        $someInput->matrix = [[1, null]];
        $someInput->nested = $nestedInput;

        $answer = 42;

        TakeSomeInput::mock()
            ->expects('execute')
            ->once()
            ->withArgs(fn (SomeInput $input): bool => $input == $someInput)
            ->andReturn(TakeSomeInput\TakeSomeInputResult::fromStdClass((object) [
                'data' => (object) [
                    'takeSomeInput' => $answer,
                ],
            ]));

        $result = TakeSomeInput::execute($someInput)->errorFree();
        self::assertSame($answer, $result->data->takeSomeInput);
    }

    public function testMake(): void
    {
        // TODO use named arguments in PHP 8
        $input = SomeInput::make(
            /* required: */
            'foo',
            /* matrix: */
            [[]],
            /* optional: */
            null,
            /* nested: */
            SomeInput::make(
                /* required: */
                'bar',
                /* matrix: */
                [[1, null]],
                /* optional: */
                'baz'
            ),
        );

        self::assertEquals(
            (object) [
                'required' => 'foo',
                'matrix' => [[]],
                'optional' => null,
                'nested' => (object) [
                    'required' => 'bar',
                    'matrix' => [[1, null]],
                    'optional' => 'baz',
                ],
            ],
            (new SomeInput())->toGraphQL($input)
        );
    }
}
