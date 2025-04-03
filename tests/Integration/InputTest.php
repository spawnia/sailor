<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Integration;

use Spawnia\Sailor\Client;
use Spawnia\Sailor\Configuration;
use Spawnia\Sailor\EndpointConfig;
use Spawnia\Sailor\Error\InvalidDataException;
use Spawnia\Sailor\Input\Operations\TakeList;
use Spawnia\Sailor\Input\Operations\TakeSomeInput;
use Spawnia\Sailor\Input\Types\SomeInput;
use Spawnia\Sailor\Response;
use Spawnia\Sailor\Tests\TestCase;

final class InputTest extends TestCase
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
            // @phpstan-ignore-next-line loose comparison
            ->withArgs(fn (SomeInput $input): bool => $input == $someInput)
            ->andReturn(TakeSomeInput\TakeSomeInputResult::fromData(
                TakeSomeInput\TakeSomeInput::make(
                    /* takeSomeInput: */
                    $answer,
                )
            ));

        $result = TakeSomeInput::execute($someInput)
            ->errorFree();
        self::assertSame($answer, $result->data->takeSomeInput);
    }

    public function testList(): void
    {
        $values = ['unimportant' => 1, 'foo' => 2, 0 => 3, 5 => 4];

        $response = Response::fromStdClass((object) [
            'data' => null,
        ]);

        $client = \Mockery::mock(Client::class);
        $client->expects('request')
            ->once()
            ->withArgs(fn (string $query, \stdClass $variables): bool => $query === TakeList::document()
                && $variables->values === [1, 2, 3, 4])
            ->andReturn($response);

        $endpoint = \Mockery::mock(EndpointConfig::class);
        $endpoint->expects('makeClient')
            ->once()
            ->withNoArgs()
            ->andReturn($client);
        $endpoint->expects('handleEvent')
            ->twice();

        Configuration::setEndpointFor(TakeList::class, $endpoint);

        $result = TakeList::execute($values);
        self::assertNull($result->data);
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

    public function testOptional(): void
    {
        // TODO use named arguments in PHP 8
        $input = SomeInput::make(
            /* required: */
            'foo',
            /* matrix: */
            [[]],
            // Omitting the optional properties `optional` and `nested`
        );

        self::assertEquals(
            (object) [
                'required' => 'foo',
                'matrix' => [[]],
            ],
            (new SomeInput())->toGraphQL($input)
        );
        self::assertNull($input->optional);
        self::assertNull($input->nested);
    }

    public function testAccessUnknownProperty(): void
    {
        // TODO use named arguments in PHP 8
        $input = SomeInput::make(
            /* required: */
            'foo',
            /* matrix: */
            [[]],
        );

        $this->expectExceptionObject(new InvalidDataException('input: Unknown property nonExistent, available properties: required, matrix, optional, nested.'));
        $input->nonExistent; // @phpstan-ignore-line intentionally wrong
    }
}
