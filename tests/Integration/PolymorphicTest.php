<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Integration;

use Spawnia\PHPUnitAssertFiles\AssertDirectory;
use Spawnia\Sailor\Codegen\Generator;
use Spawnia\Sailor\Codegen\Writer;
use Spawnia\Sailor\EndpointConfig;
use Spawnia\Sailor\Polymorphic\UserOrPost;
use Spawnia\Sailor\Polymorphic\UserOrPost\UserOrPost\User;
use Spawnia\Sailor\Polymorphic\UserOrPost\UserOrPostResult;
use Spawnia\Sailor\Tests\TestCase;

class PolymorphicTest extends TestCase
{
    use AssertDirectory;

    const EXAMPLES_PATH = __DIR__.'/../../examples/polymorphic/';

    public function testGeneratesPolymorphicExample(): void
    {
        $endpoint = self::polymorphicEndpoint();

        $generator = new Generator($endpoint, 'polymorphic');
        $files = $generator->generate();

        $writer = new Writer($endpoint);
        $writer->write($files);

        self::assertDirectoryEquals(self::EXAMPLES_PATH.'expected', self::EXAMPLES_PATH.'generated');
    }

    protected static function polymorphicEndpoint(): EndpointConfig
    {
        $fooConfig = include self::EXAMPLES_PATH.'sailor.php';

        return $fooConfig['polymorphic'];
    }

    public function testUserOrPost(): void
    {
        $id = '1';
        $name = 'blarg';

        UserOrPost::mock()
            ->expects('execute')
            ->once()
            ->with()
            ->andReturn(UserOrPostResult::fromStdClass((object) [
                'data' => (object) [
                    'userOrPost' => (object) [
                        '__typename' => 'User',
                        'id' => $id,
                        'name' => $name,
                    ],
                ],
            ]));

        $result = UserOrPost::execute()->assertErrorFree();
        $user = $result->data->userOrPost;

        self::assertInstanceOf(User::class, $user);
        self::assertSame($id, $user->id);
        self::assertSame($name, $user->name);
    }
}
